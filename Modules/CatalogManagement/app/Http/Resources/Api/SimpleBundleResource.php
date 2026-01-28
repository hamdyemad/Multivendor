<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CatalogManagement\app\Http\Resources\Api\BundleCategoryResource;
use Modules\Vendor\app\Http\Resources\Api\LightVendorResource;
use App\Helpers\PointsHelper;

class SimpleBundleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // Calculate bundle products count from loaded relationship or use pre-calculated value
        $bundleProductsCount = $this->bundle_products_count 
            ?? ($this->relationLoaded('bundleProducts') ? $this->bundleProducts->count() : 0);
        
        // Calculate total price (sum of prices without min_quantity multiplier)
        $totalPrice = $this->total_price_sum 
            ?? ($this->relationLoaded('bundleProducts') 
                ? $this->bundleProducts->sum('price') 
                : 0);

        $totalPoints = PointsHelper::calculatePoints($totalPrice);

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'slug' => $this->slug,
            'country_id' => $this->country_id,
            // Translations
            'name' => $this->name,
            'description' => $this->getTranslation('description', app()->getLocale()),
            'image' => $this->whenLoaded('main_image', function() {
                return $this->main_image ? asset('storage/' . $this->main_image->path) : '';
            }, ''),
            'category' => $this->whenLoaded('bundleCategory', function() {
                return new BundleCategoryResource($this->bundleCategory);
            }),
            'bundle_products_count' => $bundleProductsCount,
            'total_price' => round($totalPrice, 2),
            'total_points' => $totalPoints,
            // Relationships
            'vendor' => $this->whenLoaded('vendor', function() {
                return new LightVendorResource($this->vendor);
            }),
        ];
    }
}
