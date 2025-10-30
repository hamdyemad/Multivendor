<?php

namespace Modules\CatalogManagement\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VariantsConfigurationRequest extends FormRequest
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
        return [
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'type' => 'nullable|in:text,color',
            'key_id' => 'required|exists:variants_configurations_keys,id',
            'parent_id' => 'nullable|exists:variants_configurations,id',
            'value' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'type' => trans('catalogmanagement::variantsconfig.type'),
            'key_id' => trans('catalogmanagement::variantsconfig.key'),
            'parent_id' => trans('catalogmanagement::variantsconfig.parent'),
            'value' => trans('catalogmanagement::variantsconfig.value'),
        ];
    }
}
