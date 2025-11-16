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
                'name' => $this->getTranslation('name', app()->getLocale()) ?? '', // select2 expects "id" + "text"
            ];
        }
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'image' => ($this->image) ? Storage::disk('public')->url($this->image) : '',
            'name' => $this->getTranslation('name', app()->getLocale()) ?? '',
            'description' => $this->getTranslation('description', app()->getLocale()) ?? '',
            'active' => $this->active,
            'activities' => ActivityApiResource::collection($this->whenLoaded('activeActivities')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
