<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Order\app\Traits\HasVariantConfigurationTree;

class OrderProductResource extends JsonResource
{
    use HasVariantConfigurationTree;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        
        // Calculate price before tax from stored taxes
        $price = (float) $this->price;
        $taxRate = $this->taxes ? $this->taxes->sum('percentage') : 0;
        $priceBeforeTax = $taxRate > 0 ? $price / (1 + $taxRate / 100) : $price;
        
        $unitPriceBeforeTax = round($priceBeforeTax / $this->quantity, 2);
        $unitPriceAfterTax = round($price / $this->quantity, 2);
        
        return [
            'id' => $this->id,
            'vendor_product_id' => $this->vendor_product_id,
            'vendor_product_variant_id' => $this->vendor_product_variant_id,
            'product' => [
                'id' => $this->vendorProduct?->product?->id,
                'name' => $this->vendorProduct?->product?->title,
                'slug' => $this->vendorProduct?->product?->slug,
                'image' => formatImage($this->vendorProduct?->product?->mainImage),
            ],
            'vendor' => [
                'id' => $this->vendorProduct?->vendor?->id,
                'name' => $this->vendorProduct?->vendor?->getTranslation('name', $locale),
            ],
            'variant' => [
                'id' => $this->vendorProductVariant?->id,
                'sku' => $this->vendorProductVariant?->sku,
                'name' => $this->vendorProductVariant?->{"variant_path_{$locale}"},
            ],
            'configuration_tree' => $this->when(
                $this->vendorProductVariant && 
                $this->vendorProductVariant->relationLoaded('variantConfiguration') && 
                $this->vendorProductVariant->variantConfiguration,
                function() use ($locale, $unitPriceBeforeTax, $unitPriceAfterTax) {
                    return $this->buildVariantConfigurationTree(
                        $this->vendorProductVariant->variantConfiguration, 
                        $this->vendorProductVariant->id,
                        $locale
                    );
                }
            ),
            'variant_price_before_taxes' => $unitPriceBeforeTax,
            'variant_real_price' => $unitPriceAfterTax,
            'unit_price_without_taxes' => $unitPriceBeforeTax,
            'taxes' => $this->taxes ? $this->taxes->map(function ($tax) {
                return [
                    'id' => $tax->id,
                    'tax_id' => $tax->tax_id,
                    'tax_name' => $tax->tax?->name,
                    'percentage' => (float) $tax->percentage,
                    'amount' => round((float) $tax->amount / $this->quantity, 2),
                ];
            }) : [],
            'unit_price_after_taxes' => $unitPriceAfterTax,
            'quantity' => $this->quantity,
            // 'price_before_taxes' => round($priceBeforeTax, 2),
            'total' => $price,
            // 'shipping_cost' => (float) $this->shipping_cost,
            // 'commission' => (float) $this->commission,
        ];
    }
}
