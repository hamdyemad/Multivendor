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
            'sort_number' => $this->sort_number ?? 0,
            'parent' => new CategoryApiResource($this->whenLoaded('category')),
            'image' => formatImage($this->image),
            'icon' => formatImage($this->icon),
            'products_count' => $this->active_products_count ?? $this->activeProducts()->count(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
