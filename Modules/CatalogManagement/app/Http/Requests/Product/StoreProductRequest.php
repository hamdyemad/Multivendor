<?php

namespace Modules\CatalogManagement\app\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use App\Models\Language;
use App\Models\UserType;
use Illuminate\Support\Facades\Auth;

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
            'sku' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'max_per_order' => 'required|integer|min:1',
            'video_link' => 'nullable|url',
            'bank_product_id' => 'nullable|exists:products,id',

            // Relations
            'brand_id' => 'required_without:bank_product_id|exists:brands,id',
            'department_id' => 'required_without:bank_product_id|exists:departments,id',
            'category_id' => 'required_without:bank_product_id|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',

            // Vendor validation based on user role
            'vendor_id' => $this->getVendorValidationRule(),

            // Images
            'main_image' => 'required_without:bank_product_id|image|max:5120',
            'additional_images.*' => 'nullable|image|max:5120',

            // Translations
            'translations' => 'required_without:bank_product_id|array|min:1',
            'translations.*.title' => 'required_with:translations|string|max:255',
            'translations.*.details' => 'nullable|string',
            'translations.*.summary' => 'nullable|string',
            'translations.*.features' => 'nullable|string',
            'translations.*.instructions' => 'nullable|string',
            'translations.*.extra_description' => 'nullable|string',
            'translations.*.material' => 'nullable|string',
            'translations.*.tags' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string',
            'translations.*.meta_description' => 'nullable|string',
            'translations.*.meta_keywords' => 'nullable|string',

            // Refund Settings
            'is_able_to_refund' => 'nullable|boolean',
            'refund_days' => 'nullable|integer|min:0',

            // Configuration Type
            'configuration_type' => 'required|in:simple,variants',
        ];

        // Simple product validation
        if ($configurationType === 'simple') {
            $rules = array_merge($rules, [
                'price' => 'required|numeric|min:0',
                'has_discount' => 'nullable|boolean',
                'price_before_discount' => 'nullable|numeric|min:0',
                'discount_end_date' => 'nullable|date|after:today',
                'stocks' => 'required|array',
                'stocks.*.region_id' => 'required_with:stocks|exists:regions,id',
                'stocks.*.quantity' => 'required_with:stocks|integer|min:0',
            ]);
        }

        // Variants validation
        if ($configurationType === 'variants') {
            $rules = array_merge($rules, [
                'variants' => 'required|array|min:1',
                'variants.*.sku' => 'required|string',
                'variants.*.price' => 'required|numeric|min:0',
                'variants.*.has_discount' => 'nullable|boolean',
                'variants.*.price_before_discount' => 'nullable|numeric|min:0',
                'variants.*.discount_end_date' => 'nullable|date|after:today',

                // Variant configuration (standardized field name)
                'variants.*.variant_configuration_id' => 'required|exists:variants_configurations,id',

                'variants.*.stocks' => 'required|array',
                'variants.*.stocks.*.region_id' => 'required_with:variants.*.stocks|exists:regions,id',
                'variants.*.stocks.*.quantity' => 'required_with:variants.*.stocks|integer|min:0',
            ]);
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check main product SKU uniqueness
            $sku = $this->input('sku');
            if (!empty($sku)) {
                // Check if SKU exists in database (for new products, no exclusion needed)
                $exists = \Modules\CatalogManagement\app\Models\VendorProduct::withoutGlobalScopes()
                    ->where('sku', $sku)
                    ->exists();
                
                if ($exists) {
                    $validator->errors()->add('sku', __('catalogmanagement::product.sku_unique'));
                }
            }
            
            // Check variant SKUs uniqueness
            $variants = $this->input('variants', []);
            
            foreach ($variants as $index => $variant) {
                if (!empty($variant['sku'])) {
                    // Check if variant SKU exists in database
                    $exists = \Modules\CatalogManagement\app\Models\VendorProductVariant::withoutGlobalScopes()
                        ->where('sku', $variant['sku'])
                        ->exists();
                    
                    if ($exists) {
                        $validator->errors()->add(
                            "variants.{$index}.sku",
                            __('catalogmanagement::product.sku_unique')
                        );
                    }
                }
            }
        });
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
            'vendor_id' => __('vendor::vendor.name'),
            'department_id' => __('catalogmanagement::product.department'),
            'category_id' => __('catalogmanagement::product.category'),
            'sub_category_id' => __('catalogmanagement::product.sub_category'),
            'stocks.*.region_id' => __('areasettings::region.name'),
            'stocks.*.quantity' => __('common.quantity'),
            'main_image' => __('catalogmanagement::product.main_image'),
            'configuration_type' => __('catalogmanagement::product.configuration_type'),
            'price' => __('catalogmanagement::product.price'),
            'sku' => __('catalogmanagement::product.sku'),
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
            'is_able_to_refund' => filter_var($this->input('is_able_to_refund', false), FILTER_VALIDATE_BOOLEAN),
            'has_discount' => filter_var($this->input('has_discount', false), FILTER_VALIDATE_BOOLEAN),
        ]);

        // Handle variant discount booleans
        if ($this->has('variants')) {
            $variants = $this->input('variants', []);
            foreach ($variants as $index => $variant) {
                $variants[$index]['has_discount'] = filter_var($variant['has_discount'] ?? false, FILTER_VALIDATE_BOOLEAN);
            }
            $this->merge(['variants' => $variants]);
        }
    }

    /**
     * Get vendor validation rule based on user role
     */
    protected function getVendorValidationRule(): string
    {
        $currentUser = Auth::user();
        $userType = $currentUser->user_type_id;

        if (in_array($userType, [UserType::SUPER_ADMIN_TYPE, UserType::ADMIN_TYPE])) {
            // Admin/Super Admin must select a vendor
            return 'required|exists:vendors,id';
        } elseif ($userType === UserType::VENDOR_TYPE) {
            // Vendor can only create products for themselves (optional in form but handled in repository)
            return 'nullable|exists:vendors,id';
        }

        return 'nullable';
    }
}
