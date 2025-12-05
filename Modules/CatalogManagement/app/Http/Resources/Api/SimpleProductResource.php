<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\SystemSetting\app\Resources\CurrencyResource;
use Modules\CatalogManagement\app\Http\Resources\Api\TaxResource;

class SimpleProductResource extends JsonResource
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
            'configuration_type' => $this->product->configuration_type,
            'tags' => $this->product->tags_array,
            'currency' => CurrencyResource::make($this->product->currency),
            'meta_description' => $this->product->meta_description,
            'meta_keywords' => $this->product->meta_keywords ?? [],
            'tax' => TaxResource::make($this->tax),
            'variants' => VendorProductVariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}
