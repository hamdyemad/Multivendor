<?php

namespace Modules\AreaSettings\app\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\SystemSetting\app\Resources\CurrencyResource;

class CountryResource extends JsonResource
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
            'slug' => $this->slug,
            'code' => $this->code,
            'phone_code' => $this->phone_code,
            'currency' => CurrencyResource::make($this->whenLoaded('currency')),
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
