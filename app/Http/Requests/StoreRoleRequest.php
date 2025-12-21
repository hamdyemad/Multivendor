<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Language;

class StoreRoleRequest extends FormRequest
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
        $rules = [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permessions,id',
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'type' => 'nullable|string'
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
            'translations.*.name' => __('Role Name')
        ];

        return $attributes;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'permissions.array' => __('Permissions must be an array'),
            'permissions.*.exists' => __('Selected permission is invalid'),
        ];
    }
}
