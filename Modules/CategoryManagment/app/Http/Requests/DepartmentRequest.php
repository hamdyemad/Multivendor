<?php

namespace Modules\CategoryManagment\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'activities' => ['required', 'array'],
            'activities.*' => ['required', 'exists:activities,id'],
            'active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
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
        return [
            'translations.*.name' => __('categorymanagement::department.name'),
            'translations.*.description' => __('categorymanagement::department.description'),
            'active' => __('common.status'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'translations.required' => __('categorymanagement::department.at_least_one_translation_required'),
            'translations.*.name.required' => __('categorymanagement::department.name_required'),
            'translations.*.name.max' => __('categorymanagement::department.name_max_255'),
        ];
    }
}
