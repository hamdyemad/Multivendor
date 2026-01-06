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
            'name' => $this->name,
            'code' => strtolower($this->code),
            'slug' => $this->slug,
            'default' => $this->default,
            'phone_code' => $this->phone_code,
            'phone_length' => $this->phone_length,
            'image' => ($this->image) ? asset('storage/' . $this->image->path) : '',
            'currency' => CurrencyResource::make($this->whenLoaded('currency')),
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
