<?php

namespace Modules\SystemSetting\app\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
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
            'name' => $this->getTranslation('name', app()->getLocale()) ?? 'N/A',
            'code' => $this->getTranslation('name', app()->getLocale()) ?? 'N/A',
            'symbol' => $this->symbol,
            'use_image' => $this->use_image,
            'image' => $this->use_image && $this->image ? asset('/storage/' . $this->image) : null,
            'display' => $this->use_image && $this->image ? asset('/storage/' . $this->image) : $this->symbol,
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
