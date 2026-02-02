<?php

namespace Modules\Customer\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Region;
use Modules\AreaSettings\app\Models\Subregion;
use Modules\Customer\app\Models\CustomerAddress;

class UpdateAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'country_id' => 'sometimes|integer|exists:countries,id',
            'city_id' => 'sometimes|integer|exists:cities,id',
            'region_id' => 'sometimes|integer|exists:regions,id',
            'subregion_id' => 'sometimes|integer|exists:subregions,id',
            'is_primary' => 'sometimes|boolean',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Get existing address to use current values if not provided
            $addressId = $this->route('addressId');
            $existingAddress = CustomerAddress::find($addressId);
            
            $countryId = $this->country_id ?? ($existingAddress->country_id ?? null);
            $cityId = $this->city_id ?? ($existingAddress->city_id ?? null);
            $regionId = $this->region_id ?? ($existingAddress->region_id ?? null);

            // Validate city belongs to country
            if ($cityId && $countryId) {
                $city = City::withoutGlobalScopes()->find($cityId);
                if ($city && $city->country_id != $countryId) {
                    $validator->errors()->add('city_id', __('customer::address.city_not_in_country'));
                }
            }

            // Validate region belongs to city
            if ($regionId && $cityId) {
                $region = Region::withoutGlobalScopes()->find($regionId);
                if ($region && $region->city_id != $cityId) {
                    $validator->errors()->add('region_id', __('customer::address.region_not_in_city'));
                }
            }
        });
    }
}
