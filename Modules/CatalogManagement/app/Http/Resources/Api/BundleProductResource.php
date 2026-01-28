<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CatalogManagement\app\Models\VendorProduct;
use App\Helpers\PointsHelper;

class BundleProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // Use eager-loaded vendor product from vendorProductVariant relationship
        $vendorProduct = $this->vendorProductVariant?->vendorProduct;

        // Get vendor product data as array
        $vendorProductData = $vendorProduct ? (new VendorProductResource($vendorProduct))->toArray($request) : [];
        
        // Remove keys from vendor product that will be overridden by bundle product data
        // Also remove variants and configuration_tree as they show all variants, not just the bundle one
        unset(
            $vendorProductData['id'],
            $vendorProductData['star'],
            $vendorProductData['num_of_user_review'],
            $vendorProductData['created_at'],
            $vendorProductData['updated_at'],
            $vendorProductData['variants'],
            $vendorProductData['configuration_tree']
        );

        // Calculate bundle price with taxes
        $bundlePriceBeforeTaxes = (float) $this->price;
        $bundlePriceAfterTaxes = $bundlePriceBeforeTaxes;
        $taxAmount = 0;
        $totalTaxPercentage = 0;
        $taxes = [];
        
        // Get taxes from vendor product (already eager loaded)
        if ($vendorProduct && $vendorProduct->relationLoaded('taxes') && $vendorProduct->taxes->count() > 0) {
            $totalTaxPercentage = $vendorProduct->taxes->sum('percentage');
            $taxMultiplier = 1 + ($totalTaxPercentage / 100);
            $bundlePriceAfterTaxes = $bundlePriceBeforeTaxes * $taxMultiplier;
            $taxAmount = $bundlePriceAfterTaxes - $bundlePriceBeforeTaxes;
            
            // Build taxes array
            $taxes = $vendorProduct->taxes->map(function ($tax) use ($bundlePriceBeforeTaxes) {
                $taxValue = $bundlePriceBeforeTaxes * ($tax->percentage / 100);
                return [
                    'id' => $tax->id,
                    'name' => $tax->name,
                    'percentage' => $tax->percentage,
                    'amount' => round($taxValue, 2),
                ];
            })->toArray();
        }

        // Calculate points based on bundle price after taxes
        $points = PointsHelper::calculatePoints($bundlePriceAfterTaxes);

        // Merge vendor product data first, then bundle product data (bundle data takes precedence)
        return array_merge($vendorProductData, [
            'id' => $this->id,
            'bundle_id' => $this->bundle_id,
            'vendor_product_id' => $vendorProduct?->id,
            'vendor_product_variant_id' => $this->vendor_product_variant_id,
            'price_before_taxes' => round($bundlePriceBeforeTaxes, 2),
            'tax_amount' => round($taxAmount, 2),
            'tax_percentage' => $totalTaxPercentage,
            'taxes' => $taxes,
            'price_after_taxes' => round($bundlePriceAfterTaxes, 2),
            'points' => $points,
            'min_quantity' => $this->min_quantity,
            'is_gift' => ($this->price == 0) ? true : false,
            'limitation_quantity' => $this->limitation_quantity,
            'position' => $this->position,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Product Rating (use eager loaded counts)
            'star' => round($vendorProduct?->reviews_avg_star ?? 0, 1),
            'num_of_user_review' => $vendorProduct?->reviews_count ?? 0,

            // Vendor Product Variant Details (with bundle price override)
            'vendor_product_variant' => $this->whenLoaded('vendorProductVariant', function() use ($vendorProduct, $bundlePriceBeforeTaxes, $bundlePriceAfterTaxes, $taxAmount, $totalTaxPercentage) {
                // Set vendorProduct relation on variant so it can access taxes
                if ($vendorProduct) {
                    $this->vendorProductVariant->setRelation('vendorProduct', $vendorProduct);
                }
                // Pass bundle prices to the variant resource
                $this->vendorProductVariant->bundle_price_before_taxes = $bundlePriceBeforeTaxes;
                $this->vendorProductVariant->bundle_price_after_taxes = $bundlePriceAfterTaxes;
                $this->vendorProductVariant->bundle_tax_amount = $taxAmount;
                $this->vendorProductVariant->bundle_tax_percentage = $totalTaxPercentage;
                return new BundleVariantResource($this->vendorProductVariant);
            }),
        ]);
    }
}
