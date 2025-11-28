<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CategoryManagment\app\Http\Resources\Api\LightCategoryApiResource;
use Modules\CategoryManagment\app\Http\Resources\Api\LightDepartmentApiResource;
use Modules\CategoryManagment\app\Http\Resources\Api\LightSubCategoryApiResource;
use Modules\SystemSetting\app\Resources\CurrencyResource;
use Modules\Vendor\app\Http\Resources\Api\LightVendorResource;

class VendorProductResource extends JsonResource
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
            'vendor_id' => $this->vendor_id,
            'product_id' => $this->product_id,
            'slug' => $this->product->slug,

            'sku' => $this->sku,
            'points' => $this->points,
            'limitation' => $this->max_per_order,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'image' => $this->formatImage($this->product->mainImage),
            'name' => $this->product->title,
            'details' => $this->product->details,
            'summary' => $this->product->summary,
            'instructions' => $this->product->instructions,
            'features' => $this->product->features,
            'extras' => $this->product->extra_description,
            'matrial' => $this->product->material,
            'video_link' => $this->product->video_link,

            'star' => $this->product->average_rating ?? 0,
            'num_of_user_review' => $this->product->reviews_count ?? 0,
            'number_of_sale' => $this->sales,
            'views' => $this->views,
            'stock' => $this->total_stock ?? 0,

            'is_fav' => false,
            'size_color_type' => $this->product->configuration_type,
            'tags' => $this->product->tags_array,
            'currency' => CurrencyResource::make($this->product->currency),
            'meta_description' => $this->product->meta_description,
            'meta_keywords' => $this->product->meta_keywords ?? [],
            'vendor' => LightVendorResource::make($this->whenLoaded('vendor')),
            'brand' => LightBrandApiResource::make($this->product->brand),
            'tax' => TaxResource::make($this->tax),
            'variants' => VendorProductVariantResource::collection($this->variants),
            'department' => LightDepartmentApiResource::make($this->product->department),
            'category' => LightCategoryApiResource::make($this->product->category),
            'sub_category' => LightSubCategoryApiResource::make($this->product->subCategory),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Format image path to full URL
     */
    private function formatImage($imagePath): ?string
    {
        if (!$imagePath) {
            return null;
        }

        return url(asset('storage/' . $imagePath->path));
    }
}
