<?php

namespace Modules\CategoryManagment\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CategoryManagment\app\Http\Resources\Api\SubCategoryApiResource;

class CategoryApiResource extends JsonResource
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
            'slug' => $this->slug,
            'name' => $this->name,
            'summary' => $this->description,
            'image' => formatImage($this->image),
            'icon' => formatImage($this->icon),
            'department' => new DepartmentApiResource($this->whenLoaded('department')),
            'sub_categories' => SubCategoryApiResource::collection($this->whenLoaded('activeSubs')),
            'products_count' => $this->active_products_count ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
