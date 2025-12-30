<?php

namespace Modules\Order\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\CatalogManagement\app\Models\BundleProduct;
use Modules\CatalogManagement\app\Models\OccasionProduct;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;

class AddToCartRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vendor_product_id' => ['required', 'integer', 'exists:vendor_products,id'],
            'vendor_product_variant_id' => ['required', 'integer', 'exists:vendor_product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'string', 'in:product,bundle,occasion'],
            'bundle_id' => ['required_if:type,bundle', 'integer', 'exists:bundles,id'],
            'occasion_id' => ['required_if:type,occasion', 'integer', 'exists:occasions,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'vendor_product_id.required' => __('validation.vendor_product_id_required'),
            'vendor_product_id.exists' => __('validation.vendor_product_id_not_exist'),
            'vendor_product_variant_id.required' => __('validation.vendor_product_variant_id_required'),
            'vendor_product_variant_id.exists' => __('validation.vendor_product_variant_id_not_exist'),
            'quantity.required' => __('validation.quantity_required'),
            'quantity.min' => __('validation.quantity_min'),
            'type.required' => __('validation.type_required'),
            'type.in' => __('validation.type_invalid'),
            'bundle_id.required_if' => __('validation.bundle_id_required'),
            'bundle_id.exists' => __('validation.bundle_id_not_exist'),
            'occasion_id.required_if' => __('validation.occasion_id_required'),
            'occasion_id.exists' => __('validation.occasion_id_not_exist'),
        ];
    }

    /**
     * Validate quantity against vendor product max_per_order
     * and validate product belongs to bundle/occasion
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $vendorProductId = $this->input('vendor_product_id');
            $vendorProductVariantId = $this->input('vendor_product_variant_id');
            $quantity = $this->input('quantity');
            $type = $this->input('type');
            $bundleId = $this->input('bundle_id');
            $occasionId = $this->input('occasion_id');

            // Get vendor product and check max_per_order
            $vendorProduct = VendorProduct::find($vendorProductId);
            if (!$vendorProduct) {
                $validator->errors()->add('vendor_product_id', __('validation.vendor_product_id_not_exist'));
                return;
            }

            // Validate variant belongs to the vendor product
            $variant = VendorProductVariant::where('id', $vendorProductVariantId)
                ->where('vendor_product_id', $vendorProductId)
                ->first();
            
            if (!$variant) {
                $validator->errors()->add('vendor_product_variant_id', __('validation.variant_not_belong_to_product'));
                return;
            }

            // Check if quantity exceeds max_per_order
            if ($quantity > $vendorProduct->max_per_order) {
                $validator->errors()->add(
                    'quantity',
                    __('validation.quantity_exceeds_max_per_order', [
                        'max' => $vendorProduct->max_per_order
                    ])
                );
            }

            // Validate product belongs to bundle
            if ($type === 'bundle' && $bundleId) {
                // Check if bundle is active and approved
                $bundle = \Modules\CatalogManagement\app\Models\Bundle::active()->find($bundleId);
                if (!$bundle) {
                    $validator->errors()->add(
                        'bundle_id',
                        __('validation.bundle_not_active')
                    );
                    return;
                }

                $bundleProduct = BundleProduct::where('bundle_id', $bundleId)
                    ->where('vendor_product_variant_id', $vendorProductVariantId)
                    ->first();
                
                if (!$bundleProduct) {
                    $validator->errors()->add(
                        'vendor_product_variant_id',
                        __('validation.product_not_in_bundle')
                    );
                }
            }

            // Validate product belongs to occasion
            if ($type === 'occasion' && $occasionId) {
                // Check if occasion is active and not expired
                $occasion = \Modules\CatalogManagement\app\Models\Occasion::active()->find($occasionId);
                if (!$occasion) {
                    $validator->errors()->add(
                        'occasion_id',
                        __('validation.occasion_not_active')
                    );
                    return;
                }

                $occasionProduct = OccasionProduct::where('occasion_id', $occasionId)
                    ->where('vendor_product_variant_id', $vendorProductVariantId)
                    ->first();
                
                if (!$occasionProduct) {
                    $validator->errors()->add(
                        'vendor_product_variant_id',
                        __('validation.product_not_in_occasion')
                    );
                }
            }
        });
    }
}
