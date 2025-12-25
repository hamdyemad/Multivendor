<?php

namespace Modules\Customer\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Region;
use Modules\AreaSettings\app\Models\Subregion;

class CreateAddressRequest extends FormRequest
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
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'country_id' => 'required|integer|exists:countries,id',
            'city_id' => 'required|integer|exists:cities,id',
            'region_id' => 'required|integer|exists:regions,id',
            'subregion_id' => 'nullable|integer|exists:subregions,id',
            'is_primary' => 'sometimes|boolean',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Validate city belongs to country
            if ($this->city_id && $this->country_id) {
                $city = City::withoutGlobalScopes()->find($this->city_id);
                if ($city && $city->country_id != $this->country_id) {
                    $validator->errors()->add('city_id', __('customer::address.city_not_in_country'));
                }
            }

            // Validate region belongs to city
            if ($this->region_id && $this->city_id) {
                $region = Region::withoutGlobalScopes()->find($this->region_id);
                if ($region && $region->city_id != $this->city_id) {
                    $validator->errors()->add('region_id', __('customer::address.region_not_in_city'));
                }
            }

            // Validate subregion belongs to region (if provided)
            if ($this->subregion_id && $this->region_id) {
                $subregion = Subregion::withoutGlobalScopes()->find($this->subregion_id);
                if ($subregion && $subregion->region_id != $this->region_id) {
                    $validator->errors()->add('subregion_id', __('customer::address.subregion_not_in_region'));
                }
            }
        });
    }
}
