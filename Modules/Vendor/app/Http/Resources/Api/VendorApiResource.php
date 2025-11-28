<?php

namespace Modules\Vendor\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorApiResource extends JsonResource
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
            'name' => $this->getTranslation('name', app()->getLocale()) ?? '',
            'description' => $this->getTranslation('description', app()->getLocale()) ?? '',
            'country_id' => $this->country_id,
            'country_name' => $this->whenLoaded('country', $this->country?->getTranslation('name', app()->getLocale())),
            'type' => $this->type,
            'activities' => $this->whenLoaded('activeActivities', $this->activeActivities->map(fn($activity) => [
                'id' => $activity->id,
                'name' => $activity->getTranslation('name', app()->getLocale()) ?? '',
                'slug'  => $activity->slug,
            ])),
            'logo' => $this->whenLoaded('logo', $this->logo ? asset('storage/' . $this->logo->path) : null),
            'banner' => $this->whenLoaded('banner', $this->banner ? asset('storage/' . $this->banner->path) : null),
            'active' => (bool) $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
