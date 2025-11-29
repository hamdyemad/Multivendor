<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantConfigurationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $data = [
            'id' => $this->id,
            'name' => $this->name ?? null,
            'color' => $this->color ?? null,
        ];

        // Include children with their keys if they exist
        if ($this->relationLoaded('childrenRecursive') && $this->childrenRecursive->isNotEmpty()) {
            $data['children'] = $this->childrenRecursive->map(function ($child) {
                return [
                    'key_id' => $child->key_id,
                    'key_name' => $child->key?->name,
                    'options' => VariantConfigurationResource::collection($child->childrenRecursive ?? collect()),
                ];
            });
        }

        return $data;
    }
}
