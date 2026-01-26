<?php

namespace Modules\CatalogManagement\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantsConfigurationKeyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        if ($this->select2) {
            return [
                'id' => $this->id,
                'name' => $this->getTranslation('name', $locale),
            ];
        }
        return [
            "id" => $this->id,
            "name" => $this->getTranslation('name', $locale),
            "parent_key_id" => $this->parent_key_id,
            'parent' => VariantsConfigurationKeyResource::make($this->whenLoaded('parent')),
            'children' => VariantsConfigurationKeyResource::collection($this->whenLoaded('childrenKeys')),
        ];
    }
}
