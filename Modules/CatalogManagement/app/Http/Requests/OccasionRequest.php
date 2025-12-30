<?php

namespace Modules\CatalogManagement\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OccasionRequest extends FormRequest
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
        // Check if this is an update request (PUT/PATCH) or create request (POST)
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        // Image is required only when creating, nullable when updating
        $imageRule = $isUpdate ? 'nullable' : 'required';

        // For updates, variants are optional if occasion already has products
        // For creates, variants are required
        $variantsRule = $isUpdate ? 'nullable|array' : 'required|array';

        return [
            // Basic fields
            'is_active' => 'boolean',
            'image' => $imageRule . '|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',

            'variants' => $variantsRule,
            'variants.*.vendor_product_variant_id' => 'required|exists:vendor_product_variants,id',
            'variants.*.special_price' => 'nullable|numeric',

            // Translation fields
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.sub_title' => 'nullable|string|max:255',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_description' => 'nullable|string|max:500',
            'translations.*.seo_keywords' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $messages = [
            'translations.required' => trans('catalogmanagement::occasion.translations_required'),
            'image.image' => trans('catalogmanagement::occasion.image_must_be_image'),
            'image.mimes' => trans('catalogmanagement::occasion.image_invalid_format'),
            'image.max' => trans('catalogmanagement::occasion.image_max_size'),
            'is_active.boolean' => trans('catalogmanagement::occasion.active_must_be_boolean'),
            'start_date.required' => trans('catalogmanagement::occasion.start_date_required'),
            'start_date.date' => trans('catalogmanagement::occasion.start_date_must_be_date'),
            'end_date.required' => trans('catalogmanagement::occasion.end_date_required'),
            'end_date.date' => trans('catalogmanagement::occasion.end_date_must_be_date'),
            'end_date.after_or_equal' => trans('catalogmanagement::occasion.end_date_after_start'),

            // Variants messages
            'variants.required' => trans('catalogmanagement::occasion.variants_required'),
            'variants.array' => trans('catalogmanagement::occasion.variants_must_be_array'),
            'variants.*.vendor_product_variant_id.required' => trans('catalogmanagement::occasion.variant_id_required'),
            'variants.*.vendor_product_variant_id.exists' => trans('catalogmanagement::occasion.variant_id_invalid'),
            'variants.*.special_price.numeric' => trans('catalogmanagement::occasion.special_price_must_be_number'),
        ];

        // Get all languages to create language-specific error messages
        $languages = \App\Models\Language::all();

        foreach ($languages as $language) {
            $locale = $language->code;

            // Name field messages
            $messages["translations.{$language->id}.name.required"] = trans('catalogmanagement::occasion.name_required', [], $locale);
            $messages["translations.{$language->id}.name.string"] = trans('catalogmanagement::occasion.name_must_be_string', [], $locale);
            $messages["translations.{$language->id}.name.max"] = trans('catalogmanagement::occasion.name_max_length', [], $locale);

            // Title messages
            $messages["translations.{$language->id}.title.string"] = trans('catalogmanagement::occasion.title_must_be_string', [], $locale);
            $messages["translations.{$language->id}.title.max"] = trans('catalogmanagement::occasion.title_max_length', [], $locale);

            // Sub Title messages
            $messages["translations.{$language->id}.sub_title.string"] = trans('catalogmanagement::occasion.sub_title_must_be_string', [], $locale);
            $messages["translations.{$language->id}.sub_title.max"] = trans('catalogmanagement::occasion.sub_title_max_length', [], $locale);

            // SEO Title messages
            $messages["seo.{$language->id}.title.string"] = trans('catalogmanagement::occasion.seo_title_must_be_string', [], $locale);
            $messages["seo.{$language->id}.title.max"] = trans('catalogmanagement::occasion.seo_title_max_length', [], $locale);

            // SEO Description messages
            $messages["seo.{$language->id}.description.string"] = trans('catalogmanagement::occasion.seo_description_must_be_string', [], $locale);
            $messages["seo.{$language->id}.description.max"] = trans('catalogmanagement::occasion.seo_description_max_length', [], $locale);

            // SEO Keywords messages
            $messages["seo.{$language->id}.keywords.string"] = trans('catalogmanagement::occasion.seo_keywords_must_be_string', [], $locale);
            $messages["seo.{$language->id}.keywords.max"] = trans('catalogmanagement::occasion.seo_keywords_max_length', [], $locale);
        }

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'translations.*.name' => trans('catalogmanagement::occasion.name'),
            'translations.*.title' => trans('catalogmanagement::occasion.title'),
            'translations.*.sub_title' => trans('catalogmanagement::occasion.sub_title'),
            'seo.*.title' => trans('catalogmanagement::occasion.seo_title'),
            'seo.*.description' => trans('catalogmanagement::occasion.seo_description'),
            'seo.*.keywords' => trans('catalogmanagement::occasion.seo_keywords'),
            'image' => trans('catalogmanagement::occasion.image'),
            'is_active' => trans('catalogmanagement::occasion.activation'),
            'start_date' => trans('catalogmanagement::occasion.start_date'),
            'end_date' => trans('catalogmanagement::occasion.end_date'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert is_active checkbox to boolean
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
