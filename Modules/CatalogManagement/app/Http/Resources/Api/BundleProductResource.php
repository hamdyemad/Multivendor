<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundleProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bundle_id' => $this->bundle_id,
            'vendor_product_variant_id' => $this->vendor_product_variant_id,
            'price' => $this->price,
            'min_quantity' => $this->min_quantity,
            'limitation_quantity' => $this->limitation_quantity,
            'position' => $this->position,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Vendor Product Variant Details
            'vendor_product_variant' => $this->vendorProductVariant ? [
                'id' => $this->vendorProductVariant->id,
                'sku' => $this->vendorProductVariant->sku,
                'price' => $this->vendorProductVariant->price,
                'product' => $this->vendorProductVariant->vendorProduct ? [
                    'id' => $this->vendorProductVariant->vendorProduct->product->id,
                    'name' => $this->vendorProductVariant->vendorProduct->product->name
                ] : null,
                'variant_configuration' => $this->vendorProductVariant->variantConfiguration ? [
                    'id' => $this->vendorProductVariant->variantConfiguration->id,
                    'name' => $this->vendorProductVariant->variantConfiguration->name,
                    'key' => $this->vendorProductVariant->variantConfiguration->key ? [
                        'id' => $this->vendorProductVariant->variantConfiguration->key->id,
                        'name' => $this->vendorProductVariant->variantConfiguration->key->name
                    ] : null
                ] : null
            ] : null,
        ];
    }
}
