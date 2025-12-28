<?php

namespace Modules\CatalogManagement\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundleProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $vendorProductVariant = $this->vendorProductVariant;
        $vendorProduct = $vendorProductVariant?->vendorProduct;
        $product = $vendorProduct?->product;
        $vendor = $vendorProduct?->vendor;
        $variantConfig = $vendorProductVariant?->variantConfiguration;
        
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
            'vendor_product_variant' => $vendorProductVariant ? [
                'id' => $vendorProductVariant->id,
                'sku' => $vendorProductVariant->sku,
                'price' => $vendorProductVariant->price,
                'remaining_stock' => $vendorProductVariant->remaining_stock ?? 0,
                'product' => $product ? [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->mainImage ? formatImage($product->mainImage) : null,
                ] : null,
                'vendor' => $vendor ? [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'logo' => formatImage($vendor->logo),
                ] : null,
                'variant_configuration' => $variantConfig ? [
                    'id' => $variantConfig->id,
                    'name' => $variantConfig->name,
                    'key' => $variantConfig->key ? [
                        'id' => $variantConfig->key->id,
                        'name' => $variantConfig->key->name
                    ] : null
                ] : null
            ] : null,
        ];
    }
}
