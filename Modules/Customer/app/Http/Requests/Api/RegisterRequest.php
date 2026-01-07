<?php

namespace Modules\Customer\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|unique:customers,phone',
            'lang' => 'nullable|string|in:en,ar',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'region_id' => 'required|exists:regions,id',
            'gender' => 'required|in:male,female',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate that city belongs to the country if both are provided
            if ($this->country_id && $this->city_id) {
                $city = \Modules\AreaSettings\app\Models\City::where('id', $this->city_id)
                    ->where('country_id', $this->country_id)
                    ->first();

                if (!$city) {
                    $validator->errors()->add('city_id', trans('customer::customer.city_must_belong_to_country'));
                }
            }

            // Validate that region belongs to the city if both are provided
            if ($this->city_id && $this->region_id) {
                $region = \Modules\AreaSettings\app\Models\Region::where('id', $this->region_id)
                    ->where('city_id', $this->city_id)
                    ->first();

                if (!$region) {
                    $validator->errors()->add('region_id', trans('customer::customer.region_must_belong_to_city'));
                }
            }

            // Validate phone length against country's phone_length setting
            if ($this->phone && $this->country_id) {
                $country = \Modules\AreaSettings\app\Models\Country::find($this->country_id);
                if ($country && $country->phone_length) {
                    // Remove any non-digit characters for length check
                    $phoneDigits = preg_replace('/\D/', '', $this->phone);
                    if (strlen($phoneDigits) !== $country->phone_length) {
                        $validator->errors()->add('phone', trans('customer::customer.phone_length_invalid', [
                            'length' => $country->phone_length
                        ]));
                    }
                }
            }
        });

        return $validator;
    }

    public function validated($key = null, $default = null)
    {
        return parent::validated($key, $default);
    }
}
