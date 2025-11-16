<?php

namespace Modules\AreaSettings\app\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($request->select2){
            return [
                'id' => $this->id,
                'name' => $this->name ?? $this->getTranslation('name', app()->getLocale()),
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->name ?? $this->getTranslation('name', app()->getLocale()),
            'slug' => $this->slug,
            'city' => new CityResource($this->whenLoaded('city')),
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
