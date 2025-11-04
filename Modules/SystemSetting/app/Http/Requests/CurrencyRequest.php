<?php

namespace Modules\SystemSetting\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Language;
use Illuminate\Validation\Rule;

class CurrencyRequest extends FormRequest
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
        $currencyId = $this->route('currency');
        $rules = [
            'code' => [
                'required',
                'string',
                'max:3',
                Rule::unique('currencies', 'code')
                    ->ignore($currencyId)
                    ->whereNull('deleted_at')
            ],
            'symbol' => 'required|string|max:10',
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'active' => 'nullable|boolean',
        ];

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [
            'code' => __('systemsetting::currency.currency_code'),
            'symbol' => __('systemsetting::currency.currency_symbol'),
        ];

        // Add dynamic attributes for each language
        $languages = Language::all();
        foreach ($languages as $language) {
            $attributes['translations.' . $language->id . '.name'] = __('systemsetting::currency.name_' . ($language->code == 'ar' ? 'arabic' : 'english'));
        }

        return $attributes;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $messages = [
            'code.required' => __('systemsetting::currency.validation.code_required'),
            'code.unique' => __('systemsetting::currency.validation.code_unique'),
            'code.max' => __('systemsetting::currency.validation.code_max'),
            'symbol.required' => __('systemsetting::currency.validation.symbol_required'),
            'symbol.max' => __('systemsetting::currency.validation.symbol_max'),
        ];

        // Add dynamic messages for each language
        $languages = Language::all();
        foreach ($languages as $language) {
            $messages['translations.' . $language->id . '.name.required'] = __('systemsetting::currency.validation.name_' . ($language->code == 'ar' ? 'ar' : 'en') . '_required');
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

        // Convert active value to integer
        $this->merge([
            'active' => (int) $this->input('active', 0),
        ]);
    }
}
