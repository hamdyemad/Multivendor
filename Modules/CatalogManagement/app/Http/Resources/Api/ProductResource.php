<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CategoryManagment\app\Http\Resources\Api\DepartmentApiResource;
use Modules\CategoryManagment\app\Http\Resources\Api\CategoryApiResource;
use Modules\CategoryManagment\app\Http\Resources\Api\SubCategoryApiResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'details' => $this->details,
            'features' => $this->features,
            'instructions' => $this->instructions,
            'extra_description' => $this->extra_description,
            'material' => $this->material,
            'tags' => $this->tags,
            'meta_title' => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'sku' => $this->sku,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'views' => $this->views,
            'total_sold' => $this->total_sold,
            'rating' => $this->rating,
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'department' => new DepartmentApiResource($this->whenLoaded('department')),
            'category' => new CategoryApiResource($this->whenLoaded('category')),
            'sub_category' => new SubCategoryApiResource($this->whenLoaded('subCategory')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'images' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'reviews_count' => $this->reviews_count ?? 0,
            'average_rating' => $this->average_rating ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
