<?php

namespace Modules\CategoryManagment\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        if ($request->get('select2')) {
            return [
                'id' => $this->id,
                'name' => $this->name, // select2 expects "id" + "text"
                'slug' => $this->slug,
            ];
        }
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'image' => formatImage($this->image),
            'icon' => formatImage($this->icon),
            'name' => $this->name,
            'description' => $this->description,
            'sort_number' => $this->sort_number ?? 0,
            'categories' => CategoryApiResource::collection($this->whenLoaded('activeCategories')),
            'categories_count' => $this->when(
                $this->relationLoaded('activeCategories') || isset($this->active_categories_count),
                fn() => $this->active_categories_count ?? $this->activeCategories->count()
            ),
            'products_count' => $this->active_products_count ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
