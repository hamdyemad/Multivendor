<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantConfigurationKeyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'name' => $this->{"name_{$locale}"} ?? null,
            'parent' => VariantConfigurationKeyResource::make($this->whenLoaded('parent')),
            'children' => VariantConfigurationKeyResource::collection($this->whenLoaded('childrenKeys')),
        ];
    }
}
