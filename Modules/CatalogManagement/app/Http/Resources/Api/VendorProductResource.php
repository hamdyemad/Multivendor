<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CategoryManagment\app\Http\Resources\Api\GeneralResoruce;
use Modules\CategoryManagment\app\Http\Resources\Api\LightCategoryApiResource;
use Modules\CategoryManagment\app\Http\Resources\Api\LightDepartmentApiResource;
use Modules\CategoryManagment\app\Http\Resources\Api\LightSubCategoryApiResource;
use Modules\Vendor\app\Http\Resources\Api\LightVendorResource;
use Modules\CatalogManagement\app\Http\Resources\Api\VendorProductVariantResource;
use App\Helpers\PointsHelper;

class VendorProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Use pre-loaded counts from withCount/withAvg
        $totalReviews = $this->reviews_count ?? 0;
        $avgStar = $this->reviews_avg_star ?? 0;

        // Calculate points based on minimum variant price
        $price = $this->variants?->min('price') ?? 0;
        $points = PointsHelper::calculatePoints((float) $price);

        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'product_id' => $this->product_id,
            'slug' => $this->product?->slug,
            'points' => $points,
            'sku' => $this->sku,
            'reviews_count' => $totalReviews,
            'review_avg_star' => round($avgStar, 2),
            'limitation' => $this->max_per_order,
            'status' => $this->is_featured ? __('catalogmanagement::product.featured') : __('catalogmanagement::product.active'),
            'image' => $this->whenLoaded('product', function() {
                return formatImage($this->product->mainImage);
            }),
            'name' => $this->product?->title,
            'details' => $this->product?->details,
            'summary' => $this->product?->summary,
            'instructions' => $this->product?->instructions,
            'features' => $this->product?->features,
            'extras' => $this->product?->extra_description,
            'matrial' => $this->product?->material,
            'video_link' => $this->product?->video_link,

            'number_of_sale' => $this->sales,
            'views' => $this->views,
            'stock' => $this->total_stock ?? 0,
            'booked_stock' => $this->booked_stock ?? 0,
            'allocated_stock' => $this->allocated_stock ?? 0,
            'delivered_stock' => $this->fulfilled_stock ?? 0,
            'remaining_stock' => $this->remaining_stock ?? 0,

            'is_fav' => $this->is_fav,
            'configuration_type' => $this->product?->configuration_type,
            'tags' => $this->product?->tags_array ?? [],
            'meta_description' => $this->product?->meta_description,
            'meta_keywords' => $this->product?->meta_keywords ?? [],
            'vendor' => LightVendorResource::make($this->whenLoaded('vendor')),
            'brand' => $this->whenLoaded('product', function() {
                return $this->product->brand ? GeneralResoruce::make($this->product->brand) : null;
            }),
            'taxes' => TaxResource::collection($this->whenLoaded('taxes')),
            'variants' => VendorProductVariantResource::collection($this->whenLoaded('variants')),
            'department' => $this->when($this->relationLoaded('product') && $this->product?->relationLoaded('department'), function() {
                return LightDepartmentApiResource::make($this->product->department);
            }),
            'category' => $this->when($this->relationLoaded('product') && $this->product?->relationLoaded('category'), function() {
                return LightCategoryApiResource::make($this->product->category);
            }),
            'sub_category' => $this->when($this->relationLoaded('product') && $this->product?->relationLoaded('subCategory'), function() {
                return LightSubCategoryApiResource::make($this->product->subCategory);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
