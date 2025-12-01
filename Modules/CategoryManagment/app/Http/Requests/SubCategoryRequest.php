<?php

namespace Modules\CategoryManagment\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Language;

class SubCategoryRequest extends FormRequest
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
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ];

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [
            'category_id' => __('subcategory.category'),
        ];

        $languages = Language::all();
        foreach ($languages as $language) {
            $attributes['translations.' . $language->id . '.name'] = __('subcategory.name_' . ($language->code == 'ar' ? 'arabic' : 'english'));
            $attributes['translations.' . $language->id . '.description'] = __('subcategory.description_' . ($language->code == 'ar' ? 'arabic' : 'english'));
        }

        return $attributes;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $messages = [
            'category_id.required' => __('subcategory.validation.category_required'),
        ];

        $languages = Language::all();
        foreach ($languages as $language) {
            $messages['translations.' . $language->id . '.name.required'] = __('subcategory.validation.name_' . ($language->code == 'ar' ? 'ar' : 'en') . '_required');
        }

        return $messages;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => (int) $this->input('active', 0),
        ]);
    }
}
