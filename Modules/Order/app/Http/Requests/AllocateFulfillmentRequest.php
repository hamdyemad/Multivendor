<?php

namespace Modules\Order\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AllocateFulfillmentRequest extends FormRequest
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
            'allocations' => 'required|array',
            'allocations.*.order_product_id' => 'required|exists:order_products,id',
            'allocations.*.region_id' => 'required|exists:regions,id',
            'allocations.*.quantity' => 'required|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'allocations.required' => trans('order.allocations_required'),
            'allocations.array' => trans('order.allocations_must_be_array'),
            'allocations.*.order_product_id.required' => trans('order.order_product_id_required'),
            'allocations.*.order_product_id.exists' => trans('order.order_product_id_invalid'),
            'allocations.*.region_id.required' => trans('order.region_id_required'),
            'allocations.*.region_id.exists' => trans('order.region_id_invalid'),
            'allocations.*.quantity.required' => trans('order.quantity_required'),
            'allocations.*.quantity.integer' => trans('order.quantity_must_be_integer'),
            'allocations.*.quantity.min' => trans('order.quantity_must_be_positive'),
        ];
    }
}
