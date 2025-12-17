<?php

namespace Modules\CatalogManagement\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\CatalogManagement\app\Models\Product;

class SaveBankStockRequest extends FormRequest
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
            'product_id' => 'required|integer|exists:products,id',
            'vendor_id' => 'required|integer|exists:vendors,id',
        ];

        // Add configuration-specific rules
        $product = Product::find($this->product_id);
        if ($product->configuration_type == 'simple') {
            $rules = array_merge($rules, $this->getSimpleProductRules());
        } else {
            $rules = array_merge($rules, $this->getVariantProductRules());
        }

        return $rules;
    }

    /**
     * Get validation rules for simple products
     */
    private function getSimpleProductRules(): array
    {
        $rules = [
            'price' => 'required|numeric|min:0',
            'tax_id' => 'nullable|integer|exists:taxes,id',
            'has_discount' => 'nullable',
            'vendor_product_variant_id' => 'nullable|integer',
            'stocks' => 'required|array|min:1',
            'stocks.*.region_id' => 'required|integer|exists:regions,id',
            'stocks.*.quantity' => 'required|integer|min:0',
        ];

        // Add conditional validation for discount fields when switcher is ON
        if ($this->input('has_discount') == 'on' || $this->input('has_discount') == '1' || $this->input('has_discount') == 1 || $this->input('has_discount') === true || $this->input('has_discount') === 'true') {
            $rules['price_before_discount'] = 'required|numeric|min:0|gt:price';
            $rules['discount_end_date'] = 'required|date|after:today';
        } else {
            $rules['price_before_discount'] = 'nullable|numeric|min:0';
            $rules['discount_end_date'] = 'nullable|date';
        }

        return $rules;
    }

    /**
     * Get validation rules for variant products
     */
    private function getVariantProductRules(): array
    {
        $rules = [
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|integer',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.tax_id' => 'nullable|integer|exists:taxes,id',
            'variants.*.has_discount' => 'nullable',
            'variants.*.variant_configuration_id' => 'nullable|integer',
            'variants.*.stocks' => 'required|array|min:1',
            'variants.*.stocks.*.region_id' => 'required|integer|exists:regions,id',
            'variants.*.stocks.*.quantity' => 'required|integer|min:0',
        ];

        // Add conditional validation for each variant's discount fields when switcher is ON
        if ($this->has('variants') && is_array($this->input('variants'))) {
            foreach ($this->input('variants') as $index => $variant) {
                $hasDiscount = isset($variant['has_discount']) &&
                              ($variant['has_discount'] == 'on' ||
                               $variant['has_discount'] == '1' ||
                               $variant['has_discount'] == 1 ||
                               $variant['has_discount'] === true ||
                               $variant['has_discount'] === 'true');

                if ($hasDiscount) {
                    $rules["variants.{$index}.price_before_discount"] = 'required|numeric|min:0|gt:variants.' . $index . '.price';
                    $rules["variants.{$index}.discount_end_date"] = 'required|date|after:today';
                } else {
                    $rules["variants.{$index}.price_before_discount"] = 'nullable|numeric|min:0';
                    $rules["variants.{$index}.discount_end_date"] = 'nullable|date';
                }
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => __('catalogmanagement::validation.product_id_required'),
            'product_id.exists' => __('catalogmanagement::validation.product_id_exists'),
            'vendor_id.required' => __('catalogmanagement::validation.vendor_id_required'),
            'vendor_id.exists' => __('catalogmanagement::validation.vendor_id_exists'),
            'configuration_type.required' => __('catalogmanagement::validation.configuration_type_required'),
            'configuration_type.in' => __('catalogmanagement::validation.configuration_type_in'),

            // Simple product messages
            'price.required' => __('catalogmanagement::validation.price_required'),
            'price.min' => __('catalogmanagement::validation.price_min'),
            'tax_id.exists' => __('catalogmanagement::validation.tax_id_exists'),
            'price_before_discount.required' => __('catalogmanagement::validation.price_before_discount_required'),
            'price_before_discount.numeric' => __('catalogmanagement::validation.price_before_discount_numeric'),
            'price_before_discount.min' => __('catalogmanagement::validation.price_before_discount_min'),
            'price_before_discount.gt' => __('catalogmanagement::validation.price_before_discount_gt'),
            'discount_end_date.required' => __('catalogmanagement::validation.discount_end_date_required'),
            'discount_end_date.date' => __('catalogmanagement::validation.discount_end_date_date'),
            'discount_end_date.after' => __('catalogmanagement::validation.discount_end_date_after'),
            'stocks.required' => __('catalogmanagement::validation.stocks_required'),
            'stocks.min' => __('catalogmanagement::validation.stocks_min'),
            'stocks.*.region_id.required' => __('catalogmanagement::validation.stocks_region_id_required'),
            'stocks.*.region_id.exists' => __('catalogmanagement::validation.stocks_region_id_exists'),
            'stocks.*.quantity.required' => __('catalogmanagement::validation.stocks_quantity_required'),
            'stocks.*.quantity.min' => __('catalogmanagement::validation.stocks_quantity_min'),

            // Variant product messages
            'variants.required' => __('catalogmanagement::validation.variants_required'),
            'variants.min' => __('catalogmanagement::validation.variants_min'),
            'variants.*.price.required' => __('catalogmanagement::validation.variants_price_required'),
            'variants.*.price.min' => __('catalogmanagement::validation.variants_price_min'),
            'variants.*.tax_id.exists' => __('catalogmanagement::validation.variants_tax_id_exists'),
            'variants.*.price_before_discount.required' => __('catalogmanagement::validation.variants_price_before_discount_required'),
            'variants.*.price_before_discount.numeric' => __('catalogmanagement::validation.variants_price_before_discount_numeric'),
            'variants.*.price_before_discount.min' => __('catalogmanagement::validation.variants_price_before_discount_min'),
            'variants.*.price_before_discount.gt' => __('catalogmanagement::validation.variants_price_before_discount_gt'),
            'variants.*.discount_end_date.required' => __('catalogmanagement::validation.variants_discount_end_date_required'),
            'variants.*.discount_end_date.date' => __('catalogmanagement::validation.variants_discount_end_date_date'),
            'variants.*.discount_end_date.after' => __('catalogmanagement::validation.variants_discount_end_date_after'),
            'variants.*.stocks.required' => __('catalogmanagement::validation.variants_stocks_required'),
            'variants.*.stocks.min' => __('catalogmanagement::validation.variants_stocks_min'),
            'variants.*.stocks.*.region_id.required' => __('catalogmanagement::validation.variants_stocks_region_id_required'),
            'variants.*.stocks.*.region_id.exists' => __('catalogmanagement::validation.variants_stocks_region_id_exists'),
            'variants.*.stocks.*.quantity.required' => __('catalogmanagement::validation.variants_stocks_quantity_required'),
            'variants.*.stocks.*.quantity.min' => __('catalogmanagement::validation.variants_stocks_quantity_min'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_id' => __('catalogmanagement::validation.attr_product'),
            'vendor_id' => __('catalogmanagement::validation.attr_vendor'),
            'configuration_type' => __('catalogmanagement::validation.attr_configuration_type'),
            'tax_id' => __('catalogmanagement::validation.attr_tax'),
            'price_before_discount' => __('catalogmanagement::validation.attr_price_before_discount'),
            'discount_end_date' => __('catalogmanagement::validation.attr_discount_end_date'),
            'stocks.*.region_id' => __('catalogmanagement::validation.attr_region'),
            'stocks.*.quantity' => __('catalogmanagement::validation.attr_quantity'),
            'variants.*.price' => __('catalogmanagement::validation.attr_variant_price'),
            'variants.*.tax_id' => __('catalogmanagement::validation.attr_variant_tax'),
            'variants.*.price_before_discount' => __('catalogmanagement::validation.attr_variant_price_before_discount'),
            'variants.*.discount_end_date' => __('catalogmanagement::validation.attr_variant_discount_end_date'),
            'variants.*.stocks.*.region_id' => __('catalogmanagement::validation.attr_variant_stock_region'),
            'variants.*.stocks.*.quantity' => __('catalogmanagement::validation.attr_variant_stock_quantity'),
        ];
    }
}
