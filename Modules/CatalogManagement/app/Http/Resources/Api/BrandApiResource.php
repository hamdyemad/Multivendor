<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandApiResource extends JsonResource
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
            'logo' => $this->formatImage($this->logo),
            'cover' => $this->formatImage($this->cover),
            'description' => $this->description,
            'is_active' => $this->is_active,
            'facebook' => $this->facebook_url,
            'instagram' => $this->instagram_url,
            'x' => $this->twitter_url,
            'linkedin' => $this->linkedin_url,
            'pinterest' => $this->pinterest_url
        ];
    }

    /**
     * Format image path to full URL
     */
    private function formatImage($imagePath): ?string
    {
        if (!$imagePath) {
            return null;
        }

        return url(asset('storage/' . $imagePath->path));
    }
}
