<?php

namespace Modules\CategoryManagment\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryApiResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'parent' => new CategoryApiResource($this->whenLoaded('category')),
            'parent_department' => $this->whenLoaded('category', function() {
                return $this->category->department ? new DepartmentApiResource($this->category->department) : null;
            }),
            'image' => formatImage($this->image),
            'icon' => formatImage($this->icon),
            'products_count' => $this->active_products_count ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
