<?php

namespace Modules\CategoryManagment\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'icon' => formatImage($this->icon), // TODO add icon in dashboard
            'name' => $this->name,
            'description' => $this->description,
            'activities' => ActivityApiResource::collection($this->whenLoaded('activeActivities')),
            'categories' => CategoryApiResource::collection($this->whenLoaded('activeCategories')),
            'products_count' => $this->active_products_count ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
