<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CatalogManagement\app\Http\Resources\Api\TaxResource;
use Modules\CatalogManagement\app\Http\Resources\Api\OccasionResource;
use Modules\CatalogManagement\app\Http\Resources\Api\SimpleBundleResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $bundleProduct = $this->getBundleProduct();
        $occasionProduct = $this->getOccasionProduct();
        $limit = $this->limitation($bundleProduct);
        $prices = $this->calculatePricesWithLimitation($bundleProduct, $occasionProduct);
        
        // Calculate total tax amount
        $totalTaxAmount = $prices['total_after_tax'] - $prices['total_before_tax'];
        
        // Prepare context for CartProductResource
        $productContext = [
            'type' => $this->type,
            'bundle_price' => $bundleProduct?->price,
            'occasion_price' => $occasionProduct?->special_price,
        ];
        
        $productResource = new CartProductResource($this->vendorProductVariant);
        $productResource->cartContext = $productContext;
        
        return [
            'id' => $this->id,
            'product' => $productResource,
            'type' => $this->type,
            'bundle' => ($this->bundle && $this->type === "bundle") ? new SimpleBundleResource($this->bundle) : null,
            'occasion' => ($this->occasion && $this->type === "occasion") ? new OccasionResource($this->occasion) : null,
            'limitation' => $limit[0],
            'min' => $limit[1],
            'taxes' => $this->vendorProduct && $this->vendorProduct->taxes ? $this->vendorProduct->taxes->map(function ($tax) use ($prices) {
                $amount = $prices['total_before_tax'] * ($tax->percentage / 100);
                return [
                    'id' => $tax->id,
                    'name' => $tax->name,
                    'percentage' => (float) $tax->percentage,
                    'amount' => round($amount, 2),
                ];
            }) : [],
            'quantity' => $this->quantity,
            'total_before_taxes' => round($prices['total_before_tax'], 2),
            'total_tax_amount' => round($totalTaxAmount, 2),
            'total_after_taxes' => round($prices['total_after_tax'], 2),
        ];
    }

    /**
     * Get bundle product from loaded relationship
     */
    private function getBundleProduct()
    {
        if ($this->type !== 'bundle' || !$this->bundle_id || !$this->bundle) {
            return null;
        }
        
        // Use the already loaded bundleProducts relationship
        if ($this->bundle->relationLoaded('bundleProducts')) {
            return $this->bundle->bundleProducts
                ->where('vendor_product_variant_id', $this->vendor_product_variant_id)
                ->first();
        }
        
        // Fallback to direct query if not loaded (include soft deleted)
        return \Modules\CatalogManagement\app\Models\BundleProduct::withTrashed()
            ->where('bundle_id', $this->bundle_id)
            ->where('vendor_product_variant_id', $this->vendor_product_variant_id)
            ->first();
    }

    /**
     * Get occasion product from loaded relationship
     */
    private function getOccasionProduct()
    {
        if ($this->type !== 'occasion' || !$this->occasion_id || !$this->occasion) {
            return null;
        }
        
        // Use the already loaded occasionProducts relationship
        if ($this->occasion->relationLoaded('occasionProducts')) {
            return $this->occasion->occasionProducts
                ->where('vendor_product_variant_id', $this->vendor_product_variant_id)
                ->first();
        }
        
        // Fallback to direct query if not loaded
        return \Modules\CatalogManagement\app\Models\OccasionProduct::where('occasion_id', $this->occasion_id)
            ->where('vendor_product_variant_id', $this->vendor_product_variant_id)
            ->first();
    }

    /**
     * Get limitation based on cart type
     */
    private function limitation($bundleProduct = null)
    {
        if ($this->type === 'bundle' && $bundleProduct) {
            return [
                $bundleProduct->limitation_quantity,
                $bundleProduct->min_quantity
            ];
        }

        return [$this->vendorProductVariant?->max_per_order ?? 0, 0];
    }

    /**
     * Calculate prices considering bundle limitation
     * If quantity exceeds limitation, extra items use original variant price
     */
    private function calculatePricesWithLimitation($bundleProduct = null, $occasionProduct = null)
    {
        $quantity = $this->quantity;
        $originalPriceBeforeTax = $this->vendorProductVariant?->price ?? 0;
        
        // Calculate tax rate
        $taxRate = $this->vendorProduct && $this->vendorProduct->taxes 
            ? $this->vendorProduct->taxes->sum('percentage') 
            : 0;
        $taxMultiplier = 1 + ($taxRate / 100);
        
        $originalPriceAfterTax = $originalPriceBeforeTax * $taxMultiplier;
        
        // Default: use original price
        $priceBeforeTax = $originalPriceBeforeTax;
        $priceAfterTax = $originalPriceAfterTax;
        $totalBeforeTax = $originalPriceBeforeTax * $quantity;
        $totalAfterTax = $originalPriceAfterTax * $quantity;
        
        if ($this->type === 'bundle' && $this->bundle_id && $bundleProduct) {
            $bundlePriceBeforeTax = (float) $bundleProduct->price;
            $bundlePriceAfterTax = $bundlePriceBeforeTax * $taxMultiplier;
            $limitQty = $bundleProduct->limitation_quantity ?? $quantity;
            
            // Use bundle price as the display price
            $priceBeforeTax = $bundlePriceBeforeTax;
            $priceAfterTax = $bundlePriceAfterTax;
            
            // Calculate quantities
            $bundleQty = min($quantity, $limitQty);
            $extraQty = max(0, $quantity - $limitQty);
            
            // Calculate totals
            $bundleTotalBeforeTax = $bundlePriceBeforeTax * $bundleQty;
            $extraTotalBeforeTax = $originalPriceBeforeTax * $extraQty;
            $totalBeforeTax = $bundleTotalBeforeTax + $extraTotalBeforeTax;
            
            $bundleTotalAfterTax = $bundlePriceAfterTax * $bundleQty;
            $extraTotalAfterTax = $originalPriceAfterTax * $extraQty;
            $totalAfterTax = $bundleTotalAfterTax + $extraTotalAfterTax;
        } elseif ($this->type === 'occasion' && $this->occasion_id && $occasionProduct) {
            $priceBeforeTax = (float) $occasionProduct->special_price;
            $priceAfterTax = $priceBeforeTax * $taxMultiplier;
            $totalBeforeTax = $priceBeforeTax * $quantity;
            $totalAfterTax = $priceAfterTax * $quantity;
        }
        
        return [
            'price_before_tax' => $priceBeforeTax,
            'price_after_tax' => $priceAfterTax,
            'total_before_tax' => $totalBeforeTax,
            'total_after_tax' => $totalAfterTax,
        ];
    }
}
