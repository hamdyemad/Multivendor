<?php

namespace Modules\CatalogManagement\app\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Language;

class StoreProductRequest extends FormRequest
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
        $configurationType = $this->input('configuration_type');
        
        $rules = [
            // Basic Product Information
            'sku' => 'required|string|unique:products,sku',
            'points' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'max_per_order' => 'nullable|integer|min:1',
            'video_link' => 'nullable|url',
            
            // Relations
            'brand_id' => 'nullable|exists:brands,id',
            'department_id' => 'nullable|exists:departments,id',
            'category_id' => 'nullable|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'tax_id' => 'nullable|exists:taxes,id',
            
            // Images
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            
            // Translations
            'translations' => 'required|array|min:1',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.details' => 'nullable|string',
            'translations.*.summary' => 'nullable|string',
            'translations.*.features' => 'nullable|string',
            'translations.*.instructions' => 'nullable|string',
            'translations.*.extra_description' => 'nullable|string',
            'translations.*.material' => 'nullable|string',
            'translations.*.tags' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:60',
            'translations.*.meta_description' => 'nullable|string|max:160',
            'translations.*.meta_keywords' => 'nullable|string',
            
            // Configuration Type
            'configuration_type' => 'required|in:simple,variants',
        ];

        // Simple product validation
        if ($configurationType === 'simple') {
            $rules = array_merge($rules, [
                'simple_sku' => 'required|string',
                'price' => 'required|numeric|min:0',
                'has_discount' => 'nullable|boolean',
                'price_before_discount' => 'nullable|numeric|min:0',
                'offer_end_date' => 'nullable|date|after:today',
                'stocks' => 'nullable|array',
                'stocks.*.region_id' => 'required_with:stocks|exists:regions,id',
                'stocks.*.quantity' => 'required_with:stocks|integer|min:0',
            ]);
        }

        // Variants validation
        if ($configurationType === 'variants') {
            $rules = array_merge($rules, [
                'variants' => 'required|array|min:1',
                'variants.*.sku' => 'nullable|string',
                'variants.*.price' => 'required|numeric|min:0',
                'variants.*.has_discount' => 'nullable|boolean',
                'variants.*.discount_price' => 'nullable|numeric|min:0',
                'variants.*.discount_end_date' => 'nullable|date|after:today',
                'variants.*.key_id' => 'nullable|exists:variant_keys,id',
                'variants.*.variant_id' => 'nullable|exists:variant_values,id',
                'variants.*.stock' => 'nullable|array',
                'variants.*.stock.*.region_id' => 'required_with:variants.*.stock|exists:regions,id',
                'variants.*.stock.*.quantity' => 'required_with:variants.*.stock|integer|min:0',
                'variants.*.translations' => 'nullable|array',
                'variants.*.translations.*.name' => 'nullable|string|max:255',
                'variants.*.translations.*.description' => 'nullable|string',
            ]);
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'sku' => __('catalogmanagement::product.sku'),
            'translations.*.title' => __('catalogmanagement::product.title'),
            'translations.*.details' => __('catalogmanagement::product.details'),
            'brand_id' => __('catalogmanagement::product.brand'),
            'department_id' => __('catalogmanagement::product.department'),
            'category_id' => __('catalogmanagement::product.category'),
            'sub_category_id' => __('catalogmanagement::product.sub_category'),
            'tax_id' => __('catalogmanagement::product.tax'),
            'main_image' => __('catalogmanagement::product.main_image'),
            'configuration_type' => __('catalogmanagement::product.configuration_type'),
            'price' => __('catalogmanagement::product.price'),
            'simple_sku' => __('catalogmanagement::product.sku'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $messages = [
            'sku.required' => __('catalogmanagement::product.sku_required'),
            'sku.unique' => __('catalogmanagement::product.sku_unique'),
            'translations.required' => __('catalogmanagement::product.translations_required'),
            'configuration_type.required' => __('catalogmanagement::product.configuration_type_required'),
            'price.required' => __('catalogmanagement::product.price_required'),
            'variants.required' => __('catalogmanagement::product.variants_required'),
            'variants.min' => __('catalogmanagement::product.variants_min'),
        ];

        // Add custom messages for product title translations with language names
        $languages = Language::all();
        foreach ($languages as $language) {
            $messages["translations.{$language->id}.title.required"] = 
                __('catalogmanagement::product.title_required_for_language', ['language' => $language->name]);
        }

        return $messages;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert boolean strings to actual booleans
        $this->merge([
            'is_active' => filter_var($this->input('is_active', true), FILTER_VALIDATE_BOOLEAN),
            'is_featured' => filter_var($this->input('is_featured', false), FILTER_VALIDATE_BOOLEAN),
            'has_discount' => filter_var($this->input('has_discount', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
