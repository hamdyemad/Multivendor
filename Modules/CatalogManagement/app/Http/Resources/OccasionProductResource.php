<?php

namespace Modules\CatalogManagement\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OccasionProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'occasion_id' => $this->occasion_id,
            'vendor_product_variant_id' => $this->vendor_product_variant_id,
            'special_price' => $this->special_price,
            'position' => $this->position,

            // Vendor Product Variant Information
            'vendor_product_variant' => [
                'id' => $this->vendorProductVariant?->id,
                'sku' => $this->vendorProductVariant?->sku,
                'price' => $this->vendorProductVariant?->price,
                'variant_name' => $this->vendorProductVariant?->variantConfiguration?->name ?? 'Default',
                'product_name' => $this->vendorProductVariant?->vendorProduct?->product?->name ?? '-',
            ],

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
