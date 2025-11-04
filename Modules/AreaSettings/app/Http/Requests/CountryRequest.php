<?php

namespace Modules\AreaSettings\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Language;
use Illuminate\Validation\Rule;

class CountryRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $countryId = $this->route('country');
        $rules = [
            'code' => [
                'required',
                'string',
                'max:3',
                Rule::unique('countries', 'code')
                    ->ignore($countryId)
                    ->whereNull('deleted_at')
            ],
            'phone_code' => 'required|string|max:10|regex:/^\+/',
            'currency_id' => 'required|exists:currencies,id',
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'active' => 'nullable|boolean',
        ];

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [
            'code' => __('areas/country.country_code'),
            'phone_code' => __('areas/country.phone_code'),
            'currency_id' => __('areasettings::country.currency'),
        ];

        // Add dynamic attributes for each language
        $languages = Language::all();
        foreach ($languages as $language) {
            $attributes['translations.' . $language->id . '.name'] = __('areas/country.name_' . ($language->code == 'ar' ? 'arabic' : 'english'));
        }

        return $attributes;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $messages = [
            'code.required' => __('areas/country.validation.code_required'),
            'code.unique' => __('areas/country.validation.code_unique'),
            'code.max' => __('areas/country.validation.code_max'),
            'phone_code.required' => __('areas/country.validation.phone_code_required'),
            'phone_code.max' => __('areas/country.validation.phone_code_max'),
            'phone_code.regex' => __('areas/country.validation.phone_code_format'),
            'currency_id.required' => __('areasettings::country.validation.currency_required'),
            'currency_id.exists' => __('areasettings::country.validation.currency_exists'),
        ];

        // Add dynamic messages for each language
        $languages = Language::all();
        foreach ($languages as $language) {
            $messages['translations.' . $language->id . '.name.required'] = __('areas/country.validation.name_' . ($language->code == 'ar' ? 'ar' : 'en') . '_required');
        }

        return $messages;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert code to uppercase
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper($this->code),
            ]);
        }

        // Convert active value to integer (handles hidden input + checkbox pattern)
        $this->merge([
            'active' => (int) $this->input('active', 0),
        ]);
    }
}
