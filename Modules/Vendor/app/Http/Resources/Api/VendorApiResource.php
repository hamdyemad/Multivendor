<?php

namespace Modules\Vendor\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CategoryManagment\app\Http\Resources\Api\ActivityApiResource;

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
            'name' => $this->name,
            'description' => $this->description,
            'country_id' => $this->country_id,
            'country_name' => $this->whenLoaded('country', $this->country?->name),
            'type' => $this->type,
            'activities' => ActivityApiResource::collection($this->whenLoaded('activeActivities')),
            'logo' => formatImage($this->logo),
            'banner' => formatImage($this->banner),
            'active' => (bool) $this->active,
            'facebook' => $this->facebook_url,
            'instagram' => $this->instagram_url,
            'x' => $this->twitter_url,
            'linkedin' => $this->linkedin_url,
            'pinterest' => $this->pinterest_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
