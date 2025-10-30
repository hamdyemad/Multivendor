<?php

namespace Modules\CatalogManagement\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxRequest extends FormRequest
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
        $rules = [
            'tax_rate' => 'required|numeric|min:0|max:100',
            'active' => 'required|boolean',
        ];

        // Get all languages and add validation for each language's translations
        $languages = \App\Models\Language::all();
        
        foreach ($languages as $language) {
            $rules["translations.{$language->id}.name"] = 'required|string|max:255';
        }

        return $rules;
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [
            'tax_rate' => __('Tax Rate'),
            'active' => __('Status'),
        ];

        return $attributes;
    }
}
