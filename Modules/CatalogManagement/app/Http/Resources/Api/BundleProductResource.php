<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CatalogManagement\app\Models\VendorProduct;

class BundleProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // Load the vendor product with its relations if variant is loaded
        $vendorProduct = null;
        if ($this->vendorProductVariant) {
            $vendorProduct = VendorProduct::with(['product.mainImage', 'product.brand', 'product.department', 'product.category', 'product.subCategory', 'vendor', 'taxes'])
                ->withCount('reviews')
                ->withAvg('reviews', 'star')
                ->find($this->vendorProductVariant->vendor_product_id);
        }

        return [
            'id' => $this->id,
            'bundle_id' => $this->bundle_id,
            'vendor_product_id' => $vendorProduct->id,
            'vendor_product_variant_id' => $this->vendor_product_variant_id,
            'price' => round($this->price, 2),
            'min_quantity' => $this->min_quantity,
            'is_gift' => ($this->price == 0) ? true : false,
            'limitation_quantity' => $this->limitation_quantity,
            'position' => $this->position,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Product Rating
            'star' => round($vendorProduct?->reviews_avg_star ?? 0, 1),
            'num_of_user_review' => $vendorProduct?->reviews_count ?? 0,

            // Vendor Product Variant Details
            'vendor_product_variant' => $this->whenLoaded('vendorProductVariant', function() {
                return new VendorProductVariantResource($this->vendorProductVariant);
            }),
        ];
    }
}
