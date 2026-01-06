<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Calculate price before tax from stored taxes
        $price = (float) $this->price;
        $taxRate = $this->taxes ? $this->taxes->sum('percentage') : 0;
        $priceBeforeTax = $taxRate > 0 ? $price / (1 + $taxRate / 100) : $price;
        
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
                'name' => $this->vendorProduct?->vendor?->getTranslation('name', app()->getLocale()),
            ],
            'variant' => [
                'id' => $this->vendorProductVariant?->id,
                'sku' => $this->vendorProductVariant?->sku,
                'name' => $this->vendorProductVariant?->{"variant_path_" . app()->getLocale()},
            ],
            'quantity' => $this->quantity,
            'price_before_taxes' => round($priceBeforeTax, 2),
            'price' => $price,
            'commission' => (float) $this->commission,
            'shipping_cost' => (float) $this->shipping_cost,
            'taxes' => OrderProductTaxResource::collection($this->whenLoaded('taxes')),
            'total' => $price,
        ];
    }
}
