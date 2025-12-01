<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CategoryManagment\app\Http\Resources\Api\GeneralResoruce;
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
            'points' => $this->points ?? 0,
            'sku' => $this->sku,
            'star' => $this->average_rating ?? 0,
            'num_of_user_review' => $this->reviews_count ?? 0,
            'limitation' => $this->max_per_order,
            'status' => $this->is_featured ? __('catalogmanagement::product.featured') : __('catalogmanagement::product.active'),
            'image' => formatImage($this->product->mainImage),
            'name' => $this->product->title,
            'details' => $this->product->details,
            'summary' => $this->product->summary,
            'instructions' => $this->product->instructions,
            'features' => $this->product->features,
            'extras' => $this->product->extra_description,
            'matrial' => $this->product->material,
            'video_link' => $this->product->video_link,


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
            'brand' => GeneralResoruce::make($this->product->brand),
            'tax' => TaxResource::make($this->tax),
            'variants' => $this->relationLoaded('highestDiscountVariant')
                ? VendorProductVariantResource::collection(collect([$this->highestDiscountVariant])->filter())
                : VendorProductVariantResource::collection($this->whenLoaded('variants')),
            'department' => GeneralResoruce::make($this->product->department),
            'category' => GeneralResoruce::make($this->product->category),
            'sub_category' => GeneralResoruce::make($this->product->subCategory),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
