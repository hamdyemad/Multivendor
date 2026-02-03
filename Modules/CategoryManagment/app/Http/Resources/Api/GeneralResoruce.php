<?php

namespace Modules\CategoryManagment\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralResoruce extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->name,
            'slug' => $this->slug,
            'image' => formatImage($this->image),
            'icon' => formatImage($this->icon),
            'cover' => formatImage($this->cover),
            
            'type' => $this->type ?? 'General',
        ];
    }
}
