<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Order\app\Models\VendorOrderStage;
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
        
        // Get vendor stage for this order product
        $vendorId = $this->vendorProduct?->vendor?->id ?? $this->vendor_id;
        $stage = $this->getVendorStage($vendorId);
        
        return [
            'id' => $this->id,
            'vendor_product_id' => $this->vendor_product_id,
            'vendor_product_variant_id' => $this->vendor_product_variant_id,
            'product' => [
                'id' => $this->vendorProduct?->product?->id,
                'name' => $this->vendorProduct?->product?->title,
                'slug' => $this->vendorProduct?->product?->slug,
                'image' => formatImage($this->vendorProduct?->product?->mainImage),
                'refund' => [
                    'is_able_to_refund' => $this->vendorProduct?->is_able_to_refund,
                    'refund_days' => get_refund_days($this->vendorProduct),
                    'is_eligible_for_refund' => is_eligible_for_refund($this->vendorProduct, $this->delivered_at),
                    'refund_deadline' => get_refund_deadline($this->vendorProduct, $this->delivered_at),
                    'remaining_refund_days' => get_remaining_refund_days($this->vendorProduct, $this->delivered_at),
                ],
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
            'stage' => $stage,
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
    
    /**
     * Get vendor stage for this order product
     */
    private function getVendorStage(?int $vendorId): ?array
    {
        if (!$vendorId || !$this->order_id) {
            return null;
        }
        
        $vendorOrderStage = VendorOrderStage::where('order_id', $this->order_id)
            ->where('vendor_id', $vendorId)
            ->with(['stage' => function($q) {
                $q->withoutGlobalScopes();
            }])
            ->first();
        
        if (!$vendorOrderStage || !$vendorOrderStage->stage) {
            return null;
        }
        
        return [
            'id' => $vendorOrderStage->stage->id,
            'name' => $vendorOrderStage->stage->name,
            'color' => $vendorOrderStage->stage->color,
            'type' => $vendorOrderStage->stage->type,
        ];
    }
}
