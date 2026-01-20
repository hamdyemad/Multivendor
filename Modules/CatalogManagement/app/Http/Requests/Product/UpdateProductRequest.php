<?php

namespace Modules\CatalogManagement\app\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use App\Models\Language;
use App\Models\UserType;
use Illuminate\Support\Facades\Auth;

class UpdateProductRequest extends FormRequest
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
            'sku' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'max_per_order' => 'nullable|integer|min:1',
            'video_link' => 'nullable|url',

            // Relations
            'brand_id' => 'nullable|exists:brands,id',
            'department_id' => 'nullable|exists:departments,id',
            'category_id' => 'nullable|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',

            // Vendor validation based on user role
            'vendor_id' => $this->getVendorValidationRule(),

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
            'translations.*.meta_title' => 'nullable|string',
            'translations.*.meta_description' => 'nullable|string',
            'translations.*.meta_keywords' => 'nullable|string',

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
                'stocks' => 'nullable|array',
                'stocks.*.id' => 'nullable|integer|exists:vendor_product_variant_stocks,id',
                'stocks.*.region_id' => 'required|exists:regions,id',
                'stocks.*.quantity' => 'required|integer|min:0',
            ]);
        }

        // Variants validation
        if ($configurationType === 'variants') {
            $rules = array_merge($rules, [
                'variants' => 'required|array|min:1',
                'variants.*.id' => 'nullable|integer|exists:vendor_product_variants,id',
                'variants.*.sku' => 'nullable|string|distinct',
                'variants.*.price' => 'required|numeric|min:0',
                'variants.*.has_discount' => 'nullable|boolean',
                'variants.*.price_before_discount' => 'nullable|numeric|min:0',
                'variants.*.discount_end_date' => 'nullable|date|after:today',
                'variants.*.variant_configuration_id' => 'nullable|exists:variants_configurations,id',
                'variants.*.stocks' => 'nullable|array',
                'variants.*.stocks.*.id' => 'nullable',
                'variants.*.stocks.*.region_id' => 'required_with:variants.*.stocks|exists:regions,id',
                'variants.*.stocks.*.quantity' => 'required_with:variants.*.stocks|integer|min:0',
                'variants.*.translations' => 'nullable|array',
                'variants.*.translations.*.name' => 'nullable|string|max:255',
                'variants.*.translations.*.description' => 'nullable|string',
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
            $productId = $this->route('product');
            
            Log::info('SKU Validation Check', [
                'input_sku' => $sku,
                'route_product_id' => $productId
            ]);
            
            if (!empty($sku)) {
                // The route parameter {product} is the Product ID, we need to find the VendorProduct
                $vendorId = $this->input('vendor_id');
                
                // Find the current VendorProduct by product_id and vendor_id
                $currentVendorProduct = \Modules\CatalogManagement\app\Models\VendorProduct::withoutGlobalScopes()
                    ->where('product_id', $productId)
                    ->when($vendorId, function($q) use ($vendorId) {
                        $q->where('vendor_id', $vendorId);
                    })
                    ->first();
                
                // Check if SKU exists in database (excluding current vendor product)
                $query = \Modules\CatalogManagement\app\Models\VendorProduct::withoutGlobalScopes()
                    ->where('sku', $sku);
                
                if ($currentVendorProduct) {
                    $query->where('id', '!=', $currentVendorProduct->id);
                }
                
                $exists = $query->exists();
                Log::info('SKU Uniqueness Check', [
                    'sku' => $sku,
                    'product_id' => $productId,
                    'vendor_product_id' => $currentVendorProduct?->id,
                    'exists_in_other_products' => $exists
                ]);
                
                if ($exists) {
                    $validator->errors()->add('sku', __('catalogmanagement::product.sku_unique'));
                }
            }
            
            // Check variant SKUs
            $variants = $this->input('variants', []);
            
            foreach ($variants as $index => $variant) {
                if (!empty($variant['sku'])) {
                    $variantId = $variant['id'] ?? null;
                    
                    // Build variant description for error message
                    $variantDescription = [];
                    if (!empty($variant['size'])) {
                        $variantDescription[] = "Size: {$variant['size']}";
                    }
                    if (!empty($variant['color'])) {
                        $variantDescription[] = "Color: {$variant['color']}";
                    }
                    $variantInfo = !empty($variantDescription) 
                        ? ' (' . implode(', ', $variantDescription) . ')' 
                        : " (Variant #" . ($index + 1) . ")";
                    
                    // Check if SKU exists in database (excluding current variant)
                    $query = \Modules\CatalogManagement\app\Models\VendorProductVariant::withoutGlobalScopes()
                        ->where('sku', $variant['sku']);
                    
                    if ($variantId) {
                        $query->where('id', '!=', $variantId);
                    }
                    
                    if ($query->exists()) {
                        $validator->errors()->add(
                            "variants.{$index}.sku",
                            __('catalogmanagement::product.sku_unique') . $variantInfo . " - SKU: {$variant['sku']}"
                        );
                    }
                }
            }
        });
    }

    /**
     * Get the validation data from the request.
     */
    public function validationData()
    {
        $data = parent::validationData();

        \Log::info('Original request data:', $data);

        // Clean up stock data - remove empty stock entries
        if (isset($data['stocks'])) {
            $originalStocks = $data['stocks'];
            $data['stocks'] = $this->filterEmptyStocks($data['stocks']);
            \Log::info('Filtered simple stocks:', ['original' => $originalStocks, 'filtered' => $data['stocks']]);
        }

        // Clean up variant stock data
        if (isset($data['variants'])) {
            foreach ($data['variants'] as $variantIndex => $variant) {
                if (isset($variant['stocks'])) {
                    $originalStocks = $variant['stocks'];
                    $data['variants'][$variantIndex]['stocks'] = $this->filterEmptyStocks($variant['stocks']);
                    \Log::info("Filtered variant {$variantIndex} stocks:", ['original' => $originalStocks, 'filtered' => $data['variants'][$variantIndex]['stocks']]);
                }
            }
        }

        \Log::info('Final filtered data:', $data);
        return $data;
    }

    /**
     * Filter out empty stock entries
     */
    protected function filterEmptyStocks($stocks)
    {
        if (!is_array($stocks)) {
            return [];
        }

        return array_values(array_filter($stocks, function ($stock) {
            // Keep stock entry only if it has both region_id and quantity
            // This filters out incomplete entries completely

            $hasRegionAndQuantity = !empty($stock['region_id']) &&
                                  (isset($stock['quantity']) && $stock['quantity'] !== '' && $stock['quantity'] !== null);

            return $hasRegionAndQuantity;
        }));
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
            'main_image' => __('catalogmanagement::product.main_image'),
            'configuration_type' => __('catalogmanagement::product.configuration_type'),
            'price' => __('catalogmanagement::product.price'),
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
