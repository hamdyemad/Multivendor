<?php

namespace Modules\CatalogManagement\app\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class UpdateStockPricingRequest extends FormRequest
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
     * Only Step 3 validation: Configuration Type, Pricing, and Stock
     */
    public function rules(): array
    {
        $configurationType = $this->input('configuration_type');

        $rules = [
            // Configuration Type (Step 3)
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
                'variants.*.sku' => 'nullable|string',
                'variants.*.price' => 'required|numeric|min:0',
                'variants.*.has_discount' => 'nullable|boolean',
                'variants.*.price_before_discount' => 'nullable|numeric|min:0',
                'variants.*.discount_end_date' => 'nullable|date|after:today',
                'variants.*.variant_configuration_id' => 'nullable|exists:variants_configurations,id',
                'variants.*.stocks' => 'nullable|array',
                'variants.*.stocks.*.id' => 'nullable',
                'variants.*.stocks.*.region_id' => 'required_with:variants.*.stocks|exists:regions,id',
                'variants.*.stocks.*.quantity' => 'required_with:variants.*.stocks|integer|min:0',
            ]);
        }

        return $rules;
    }

    /**
     * Get the validation data from the request.
     */
    public function validationData()
    {
        $data = parent::validationData();

        Log::info('Stock Pricing Update - Original request data:', $data);

        // Clean up stock data - remove empty stock entries
        if (isset($data['stocks'])) {
            $originalStocks = $data['stocks'];
            $data['stocks'] = $this->filterEmptyStocks($data['stocks']);
            Log::info('Filtered simple stocks:', ['original' => $originalStocks, 'filtered' => $data['stocks']]);
        }

        // Clean up variant stock data
        if (isset($data['variants'])) {
            foreach ($data['variants'] as $variantIndex => $variant) {
                if (isset($variant['stocks'])) {
                    $originalStocks = $variant['stocks'];
                    $data['variants'][$variantIndex]['stocks'] = $this->filterEmptyStocks($variant['stocks']);
                    Log::info("Filtered variant {$variantIndex} stocks:", ['original' => $originalStocks, 'filtered' => $data['variants'][$variantIndex]['stocks']]);
                }
            }
        }

        Log::info('Stock Pricing Update - Final filtered data:', $data);
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
            'configuration_type' => __('catalogmanagement::product.configuration_type'),
            'price' => __('catalogmanagement::product.price'),
            'stocks.*.region_id' => __('catalogmanagement::product.region'),
            'stocks.*.quantity' => __('catalogmanagement::product.quantity'),
            'variants.*.price' => __('catalogmanagement::product.variant_price'),
            'variants.*.stocks.*.region_id' => __('catalogmanagement::product.region'),
            'variants.*.stocks.*.quantity' => __('catalogmanagement::product.quantity'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'configuration_type.required' => __('catalogmanagement::product.configuration_type_required'),
            'price.required' => __('catalogmanagement::product.price_required'),
            'price.numeric' => __('catalogmanagement::product.price_numeric'),
            'price.min' => __('catalogmanagement::product.price_min'),
            'variants.required' => __('catalogmanagement::product.variants_required'),
            'variants.min' => __('catalogmanagement::product.variants_min'),
            'variants.*.price.required' => __('catalogmanagement::product.variant_price_required'),
            'stocks.*.region_id.required' => __('catalogmanagement::product.region_required'),
            'stocks.*.quantity.required' => __('catalogmanagement::product.quantity_required'),
            'stocks.*.quantity.integer' => __('catalogmanagement::product.quantity_integer'),
            'stocks.*.quantity.min' => __('catalogmanagement::product.quantity_min'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert boolean strings to actual booleans
        $this->merge([
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
}
