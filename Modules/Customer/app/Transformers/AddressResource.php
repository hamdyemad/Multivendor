<?php

namespace Modules\Customer\app\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\AreaSettings\app\Resources\CountryResource;
use Modules\AreaSettings\app\Resources\CityResource;
use Modules\AreaSettings\app\Resources\RegionResource;
use Modules\AreaSettings\app\Resources\SubregionResource;

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
            "is_primary" => $this->is_primary,
            "country" => CountryResource::make($this->country),
            "city" => CityResource::make($this->city),
            "region" => RegionResource::make($this->region),
            "subregion" => SubregionResource::make($this->subregion),
        ];
    }
}
