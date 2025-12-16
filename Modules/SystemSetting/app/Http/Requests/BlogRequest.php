<?php

namespace Modules\SystemSetting\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
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
            'blog_category_id' => 'required|exists:blog_categories,id',
            'active' => 'sometimes|boolean',
            'image' => 'sometimes|file|image|mimes:jpeg,jpg,png,gif|max:2048',
        ];

        // Translation rules
        $languages = \App\Models\Language::all();
        foreach ($languages as $language) {
            $rules["translations.{$language->id}.title"] = 'required|string|max:255';
            $rules["translations.{$language->id}.content"] = 'required|string';
            $rules["translations.{$language->id}.meta_title"] = 'nullable|string|max:255';
            $rules["translations.{$language->id}.meta_description"] = 'nullable|string|max:500';
            $rules["translations.{$language->id}.meta_keywords"] = 'nullable|array';
            $rules["translations.{$language->id}.meta_keywords.*"] = 'nullable|string|max:100';
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [
            'blog_category_id' => __('systemsetting::blogs.category'),
        ];

        $languages = \App\Models\Language::all();
        foreach ($languages as $language) {
            $attributes["translations.{$language->id}.title"] = __('systemsetting::blogs.title') . ' (' . $language->name . ')';
            $attributes["translations.{$language->id}.content"] = __('systemsetting::blogs.content') . ' (' . $language->name . ')';
            $attributes["translations.{$language->id}.meta_title"] = __('systemsetting::blogs.meta_title') . ' (' . $language->name . ')';
            $attributes["translations.{$language->id}.meta_description"] = __('systemsetting::blogs.meta_description') . ' (' . $language->name . ')';
            $attributes["translations.{$language->id}.meta_keywords"] = __('systemsetting::blogs.meta_keywords') . ' (' . $language->name . ')';
        }

        return $attributes;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->has('active') ? $this->boolean('active') : true,
        ]);

        // Clean meta keywords array but keep it as array for validation
        if ($this->has('translations')) {
            $translations = $this->input('translations');
            foreach ($translations as $languageId => $translation) {
                if (isset($translation['meta_keywords'])) {
                    if (is_array($translation['meta_keywords'])) {
                        // Filter out empty values and re-index
                        $keywords = array_values(array_filter($translation['meta_keywords'], function($value) {
                            return !empty($value) && is_string($value);
                        }));
                        $translations[$languageId]['meta_keywords'] = $keywords;
                    } elseif (is_string($translation['meta_keywords'])) {
                        // If it comes as string (e.g. "tag1, tag2"), convert to array
                        $keywords = array_map('trim', explode(',', $translation['meta_keywords']));
                        $keywords = array_values(array_filter($keywords));
                        $translations[$languageId]['meta_keywords'] = $keywords;
                    }
                }
            }
            $this->merge(['translations' => $translations]);
        }
    }

    /**
     * Handle a failed validation attempt for AJAX requests.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson() || $this->ajax()) {
            throw new \Illuminate\Validation\ValidationException(
                $validator,
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()->toArray(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
