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
            'price' => round($this->price, 2),
            'min_quantity' => $this->min_quantity,
            'is_gift' => ($this->price == 0) ? true : false,
            'limitation_quantity' => $this->limitation_quantity,
            'position' => $this->position,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Vendor Product Variant Details
            'vendor_product_variant' => $this->whenLoaded('vendorProductVariant', function() {
                return new VendorProductVariantResource($this->vendorProductVariant);
            })
        ];
    }
}
