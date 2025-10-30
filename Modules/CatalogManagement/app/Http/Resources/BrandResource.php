<?php

namespace Modules\CatalogManagement\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
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
            'name' => $this->getTranslation('name', app()->getLocale()) ?? '',
            'active' => $this->active,
            'facebook_url' => $this->facebook_url ?? "",
            'instagram_url' => $this->instagram_url ?? "",
            'twitter_url' => $this->twitter_url ?? "",
            'linkedin_url' => $this->linkedin_url ?? "",
            'youtube_url' => $this->youtube_url ?? "",
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
