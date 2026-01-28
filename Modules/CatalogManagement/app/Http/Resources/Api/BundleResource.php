<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Vendor\app\Http\Resources\Api\VendorApiResource;
use Modules\CatalogManagement\app\Http\Resources\Api\BundleProductResource;
use Modules\CatalogManagement\app\Http\Resources\Api\BundleCategoryResource;
use App\Helpers\PointsHelper;

class BundleResource extends JsonResource
{
    /**
     * Flag to include bundle products (for show endpoint)
     */
    public bool $includeProducts = false;

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $totalPrice = round($this->bundleTotalPrice(), 2);
        $totalPoints = PointsHelper::calculatePoints($totalPrice);

        $data = [
            'id' => $this->id,
            'sku' => $this->sku,
            'slug' => $this->slug,
            'country_id' => $this->country_id,
            // Translations
            'name' => $this->name,
            'description' => $this->description,
            'image' => ($this->main_image) ? asset('storage/' . $this->main_image->path) : '',
            'seo_title' => $this->getTranslation('seo_title') ?? '',
            'seo_description' => $this->getTranslation('seo_description') ?? '',
            'seo_keywords' => $this->getTranslation('seo_keywords') ?? '',
            'category' => $this->when('bundleCategory', function() {
                return new BundleCategoryResource($this->bundleCategory);
            }),
            'bundle_products_count' => $this->bundle_products_count ?? 0,
            'total_price' => $totalPrice,
            'total_points' => $totalPoints,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        // Only include bundle_products for single bundle (show endpoint)
        if ($this->includeProducts) {
            $data['bundle_products'] = $this->when('bundleProducts', function() {
                return BundleProductResource::collection($this->bundleProducts);
            });
        }

        return $data;
    }

    /**
     * Get all translations for a field
     */
    private function getTranslations($field)
    {
        $translations = [];
        foreach (['en', 'ar'] as $lang) {
            $translations[$lang] = $this->getTranslation($field, $lang);
        }
        return $translations;
    }
}
