<?php

namespace Modules\Order\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\CatalogManagement\app\Models\Bundle;
use Modules\CatalogManagement\app\Models\BundleProduct;
use Modules\CatalogManagement\app\Models\Occasion;
use Modules\CatalogManagement\app\Models\VendorProduct;

class AddBulkToCartRequest extends FormRequest
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.vendor_product_id' => ['required', 'integer', 'exists:vendor_products,id'],
            'items.*.vendor_product_variant_id' => ['required', 'integer', 'exists:vendor_product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.type' => ['required', 'string', 'in:product,bundle,occasion'],
            'items.*.bundle_id' => ['required_if:type,bundle', 'integer', 'exists:bundles,id'],
            'items.*.occasion_id' => ['required_if:type,occasion', 'integer', 'exists:occasions,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'items.*.bundle_id.required' => __('validation.bundle_id_required'),
            'items.*.bundle_id.exists' => __('validation.bundle_id_not_exist'),
            'items.required' => __('validation.items_required'),
            'items.min' => __('validation.items_min'),
            'items.*.vendor_product_id.required' => __('validation.vendor_product_id_required'),
            'items.*.vendor_product_id.exists' => __('validation.vendor_product_id_not_exist'),
            'items.*.vendor_product_variant_id.required' => __('validation.vendor_product_variant_id_required'),
            'items.*.vendor_product_variant_id.exists' => __('validation.vendor_product_variant_id_not_exist'),
            'items.*.quantity.required' => __('validation.quantity_required'),
            'items.*.quantity.min' => __('validation.quantity_min'),
        ];
    }

    /**
     * Validate bundle exists and items are valid
     * and validate remaining stock (including existing cart items)
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            $customerId = $this->user()->id;

            // Validate each item in the bundle
            foreach ($items as $index => $item) {
                $vendorProductId = $item['vendor_product_id'] ?? null;
                $variantId = $item["vendor_product_variant_id"] ?? null;
                $quantity = $item['quantity'] ?? 0;
                $type = $item["type"] ?? "product";
                $bundleId = $item['bundle_id'] ?? null;
                $occasionId = $item['occasion_id'] ?? null;

                $vendorProduct = VendorProduct::find($vendorProductId);
                if (!$vendorProduct) {
                    $validator->errors()->add(
                        "items.$index.vendor_product_id",
                        __('validation.vendor_product_id_not_exist')
                    );
                    continue;
                }

                // Get variant and check remaining stock including existing cart items
                $variant = \Modules\CatalogManagement\app\Models\VendorProductVariant::find($variantId);
                if ($variant) {
                    $remainingStock = $variant->remaining_stock ?? 0;
                    
                    // Get existing cart quantity for this variant
                    $existingCartQuantity = \Modules\Order\app\Models\Cart::where('customer_id', $customerId)
                        ->where('vendor_product_variant_id', $variantId)
                        ->where('type', $type)
                        ->where('bundle_id', $bundleId)
                        ->where('occasion_id', $occasionId)
                        ->sum('quantity');
                    
                    // Total quantity = existing cart + new request
                    $totalQuantity = $existingCartQuantity + $quantity;
                    
                    if ($totalQuantity > $remainingStock) {
                        $validator->errors()->add(
                            "items.$index.quantity",
                            __('validation.quantity_exceeds_available_stock_with_cart', [
                                'available' => $remainingStock,
                                'in_cart' => $existingCartQuantity,
                                'remaining' => max(0, $remainingStock - $existingCartQuantity)
                            ])
                        );
                    }
                }

                // Check if bundle exists and is active
                if ($type == "bundle" && $bundleId) {
                    $bundle = Bundle::active()->find($bundleId);
                    if (!$bundle) {
                        $validator->errors()->add(
                            "items.$index.bundle_id",
                            __('validation.bundle_not_active')
                        );
                        continue;
                    }

                    // Validate bundle product exists
                    $bundleProduct = BundleProduct::where(function ($query) use ($bundleId, $variantId) {
                        $query->where('bundle_id', $bundleId)
                            ->where('vendor_product_variant_id', $variantId);
                    })->first();

                    if(!$bundleProduct)
                    {
                        $validator->errors()->add(
                            "items.$index.bundle_id",
                            __('validation.bundle_not_active')
                        );
                        continue;
                    }

                    // Validate minimum quantity only (no max limit - extra items use original price)
                    if ($bundleProduct && $quantity < $bundleProduct->min_quantity) {
                        $validator->errors()->add(
                            "items.$index.quantity",
                            __('validation.quantity_below_bundle_min', [
                                'min' => $bundleProduct->min_quantity
                            ])
                        );
                    }
                }

                // Check if occasion exists and is active
                if ($type == "occasion" && $occasionId) {
                    $occasion = Occasion::active()->find($occasionId);
                    if (!$occasion) {
                        $validator->errors()->add(
                            "items.$index.occasion_id",
                            __('validation.occasion_not_active')
                        );
                        continue;
                    }
                }

                // Check if quantity exceeds max_per_order for regular products
                if ($type == "product" && $quantity > $vendorProduct->max_per_order) {
                    $validator->errors()->add(
                        "items.$index.quantity",
                        __('validation.quantity_exceeds_max_per_order', [
                            'max' => $vendorProduct->max_per_order
                        ])
                    );
                }
            }
        });
    }
}
