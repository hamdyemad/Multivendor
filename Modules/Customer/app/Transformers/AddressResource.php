<?php

namespace Modules\Customer\app\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\AreaSettings\app\Resources\CountryResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "address" => $this->address,
            "postal_code" => $this->postal_code,
            // "latitude" => $this->latitude,
            // "longitude" => $this->longitude,
            "is_primary" => $this->is_primary,
            "country" => CountryResource::make($this->country),
            "city" => $this->city->getTranslation('name', app()->getLocale()),
            "region" => $this->region->getTranslation('name', app()->getLocale()),
            "subregion" => $this->subregion?->getTranslation('name', app()->getLocale()),
        ];
    }
}
