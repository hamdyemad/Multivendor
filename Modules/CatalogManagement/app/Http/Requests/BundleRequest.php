<?php

namespace Modules\CatalogManagement\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BundleRequest extends FormRequest
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
        return [
            'vendor_id' => 'required|exists:vendors,id',
            'bundle_category_id' => 'required|exists:bundle_categories,id',
            'sku' => 'required|string|unique:bundles,sku,' . $this->route('bundle'),
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.seo_title' => 'nullable|string|max:255',
            'translations.*.seo_description' => 'nullable|string|max:500',
            'translations.*.seo_keywords' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*.path' => 'nullable|string',
            // Bundle Products Validation
            'bundle_products' => 'required|array|min:1',
            'bundle_products.*.vendor_product_variant_id' => 'required|exists:vendor_product_variants,id',
            'bundle_products.*.price' => 'required|numeric|min:0',
            'bundle_products.*.limitation_quantity' => 'required|numeric|min:1',
            'bundle_products.*.min_quantity' => 'required|numeric|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'vendor_id.required' => trans('catalogmanagement::bundle.vendor_required'),
            'vendor_id.exists' => trans('catalogmanagement::bundle.vendor_not_exist'),
            'bundle_category_id.required' => trans('catalogmanagement::bundle.category_required'),
            'bundle_category_id.exists' => trans('catalogmanagement::bundle.category_not_exist'),
            'sku.required' => trans('catalogmanagement::bundle.sku_required'),
            'sku.unique' => trans('catalogmanagement::bundle.sku_unique'),
            'image.image' => trans('catalogmanagement::bundle.image_must_be_image'),
            'image.mimes' => trans('catalogmanagement::bundle.image_invalid_format'),
            'image.max' => trans('catalogmanagement::bundle.image_too_large'),
            'translations.required' => trans('catalogmanagement::bundle.translations_required'),
            'translations.*.name.required' => trans('catalogmanagement::bundle.name_required_each_language'),
            // Bundle Products Messages
            'bundle_products.required' => trans('catalogmanagement::bundle.bundle_products_required'),
            'bundle_products.min' => trans('catalogmanagement::bundle.bundle_products_required'),
            'bundle_products.*.vendor_product_variant_id.required' => trans('catalogmanagement::bundle.product_variant_required'),
            'bundle_products.*.vendor_product_variant_id.exists' => trans('catalogmanagement::bundle.product_not_exist'),
            'bundle_products.*.price.required' => trans('catalogmanagement::bundle.price_required'),
            'bundle_products.*.price.numeric' => trans('catalogmanagement::bundle.price_numeric'),
            'bundle_products.*.price.min' => trans('catalogmanagement::bundle.price_min'),
            'bundle_products.*.limitation_quantity.numeric' => trans('catalogmanagement::bundle.limit_quantity_numeric'),
            'bundle_products.*.limitation_quantity.min' => trans('catalogmanagement::bundle.limit_quantity_min'),
            'bundle_products.*.min_quantity.required' => trans('catalogmanagement::bundle.min_quantity_required'),
            'bundle_products.*.min_quantity.numeric' => trans('catalogmanagement::bundle.min_quantity_numeric'),
            'bundle_products.*.min_quantity.min' => trans('catalogmanagement::bundle.min_quantity_min'),
        ];
    }
}
