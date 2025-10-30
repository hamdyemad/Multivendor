<?php

namespace Modules\CatalogManagement\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantsConfigurationResource extends JsonResource
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
            'name' => $this->getTranslation('name', $locale),
            'parent' => VariantsConfigurationResource::make($this->whenLoaded('parent_data')),
            'children' => VariantsConfigurationResource::collection($this->whenLoaded('childrenRecursive')),
            'key' => VariantsConfigurationKeyResource::make($this->whenLoaded('key')),
        ];
    }
}