<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CatalogManagement\app\Http\Resources\Api\BundleCategoryResource;
use Modules\Vendor\app\Http\Resources\Api\LightVendorResource;

class SimpleBundleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'slug' => $this->slug,
            'country_id' => $this->country_id,
            // Translations
            'name' => $this->name,
            'description' => $this->description,
            'image' => ($this->main_image) ? asset('storage/' . $this->main_image->path) : '',
            'category' => $this->when('bundleCategory', function() {
                return new BundleCategoryResource($this->bundleCategory);
            }),
            'bundle_products_count' => $this->bundle_products_count ?? 0,
            'total_price' => round($this->bundleTotalPrice(), 2),
            // Relationships
            'vendor' => $this->when('vendor', function() {
                return new LightVendorResource($this->vendor);
            }),
        ];
    }
}
