<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CatalogManagement\app\Http\Resources\Api\VariantConfigurationResource;

class VariantConfigurationKeyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'key_id' => $this->id,
            'key_name' => $this->name,
            'options' => VariantConfigurationResource::collection($this->whenLoaded('variants')),
            'children' => VariantConfigurationKeyResource::collection($this->whenLoaded('childrenKeys')),
        ];
    }
}
