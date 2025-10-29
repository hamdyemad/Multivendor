<?php

namespace Modules\Brands\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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
            'active' => 'required|boolean',
            'facebook_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'pinterest_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'logo' => $this->isMethod('post') ? 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' : 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cover' => $this->isMethod('post') ? 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' : 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        // Get all languages and add validation for each language's translations
        $languages = \App\Models\Language::all();
        
        foreach ($languages as $language) {
            $rules["translations.{$language->id}.name"] = 'required|string|max:255';
            $rules["translations.{$language->id}.description"] = 'nullable|string';
        }

        return $rules;
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [
            'active' => __('brand.activation'),
            'facebook_url' => __('brand.facebook_url'),
            'linkedin_url' => __('brand.linkedin_url'),
            'pinterest_url' => __('brand.pinterest_url'),
            'twitter_url' => __('brand.twitter_url'),
            'instagram_url' => __('brand.instagram_url'),
            'logo' => __('brand.logo'),
            'cover' => __('brand.cover'),
        ];

        // $languages = \App\Models\Language::all();
        
        // foreach ($languages as $language) {
        //     $attributes["translations.{$language->id}.name"] = __('brand.name') . " ({$language->name})";
        //     $attributes["translations.{$language->id}.description"] = __('brand.description') . " ({$language->name})";
        // }

        return $attributes;
    }

    /**
     * Get custom error messages.
     */
    // public function messages(): array
    // {
    //     return [
    //         // 'translations.*.name.required' => __('brand.name_required'),
    //         // 'logo.image' => __('brand.logo_must_be_image'),
    //         // 'logo.max' => __('brand.logo_max_size'),
    //         // 'cover.image' => __('brand.cover_must_be_image'),
    //         // 'cover.max' => __('brand.cover_max_size'),
    //     ];
    // }
}
