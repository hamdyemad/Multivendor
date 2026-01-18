<?php

namespace Modules\Refund\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRefundRequestRequest extends FormRequest
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
            'order_id' => [
                'required',
                'exists:orders,id',
                function ($attribute, $value, $fail) {
                    // Validate that order belongs to the authenticated customer
                    $order = \Modules\Order\app\Models\Order::find($value);
                    if (!$order) {
                        $fail(trans('refund::refund.validation.order_invalid'));
                        return;
                    }
                    
                    $authenticatedUser = auth('sanctum')->user();
                    if ($authenticatedUser && $order->customer_id !== $authenticatedUser->id) {
                        $fail(trans('refund::refund.validation.order_not_yours'));
                        return;
                    }
                },
            ],
            'reason' => 'required|string|max:500',
            'customer_notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.order_product_id' => [
                'required',
                'exists:order_products,id',
                function ($attribute, $value, $fail) {
                    // Get the order_id from the request
                    $orderId = $this->input('order_id');
                    
                    // Validate that order_product belongs to the order
                    $orderProduct = \Modules\Order\app\Models\OrderProduct::with('product')->find($value);
                    if (!$orderProduct || $orderProduct->order_id != $orderId) {
                        $fail(trans('refund::refund.validation.order_product_not_in_order'));
                        return;
                    }
                    
                    // Validate that order_product is not already refunded
                    if ($orderProduct->is_refunded) {
                        $fail(trans('refund::refund.validation.order_product_already_refunded'));
                        return;
                    }
                    
                    // Validate that order_product is not in a pending refund request
                    $hasPendingRefund = \Modules\Refund\app\Models\RefundRequestItem::whereHas('refundRequest', function ($query) {
                        $query->whereIn('status', ['pending', 'approved', 'in_progress', 'picked_up']);
                    })->where('order_product_id', $value)->exists();
                    
                    if ($hasPendingRefund) {
                        $fail(trans('refund::refund.validation.order_product_pending_refund'));
                        return;
                    }
                    
                    // Get vendor_id from the product
                    $vendorId = $orderProduct->product->vendor_id ?? null;
                    
                    if (!$vendorId) {
                        $fail(trans('refund::refund.validation.product_no_vendor'));
                        return;
                    }
                    
                    // Check if this vendor has delivered their products
                    $vendorStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $orderId)
                        ->where('vendor_id', $vendorId)
                        ->with('stage')
                        ->first();
                    
                    if (!$vendorStage) {
                        $fail(trans('refund::refund.validation.vendor_no_stage'));
                        return;
                    }
                    
                    // Check if vendor has delivered
                    if (!$vendorStage->stage || $vendorStage->stage->type !== 'delivered') {
                        $fail(trans('refund::refund.validation.vendor_not_delivered'));
                        return;
                    }
                },
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    // Get the index from the attribute path (items.0.quantity -> 0)
                    preg_match('/items\.(\d+)\.quantity/', $attribute, $matches);
                    $index = $matches[1] ?? 0;
                    
                    // Get the order_product_id for this item
                    $orderProductId = $this->input("items.{$index}.order_product_id");
                    
                    if ($orderProductId) {
                        $orderProduct = \Modules\Order\app\Models\OrderProduct::find($orderProductId);
                        if ($orderProduct && $value > $orderProduct->quantity) {
                            $fail(trans('refund::refund.validation.quantity_exceeds_ordered', ['quantity' => $orderProduct->quantity]));
                        }
                    }
                },
            ],
            'items.*.reason' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'order_id.required' => trans('refund::refund.validation.order_required'),
            'order_id.exists' => trans('refund::refund.validation.order_invalid'),
            'reason.required' => trans('refund::refund.validation.reason_required'),
            'reason.max' => trans('refund::refund.validation.reason_max', ['max' => 500]),
            'customer_notes.max' => trans('refund::refund.validation.customer_notes_max', ['max' => 1000]),
            'items.required' => trans('refund::refund.validation.items_required'),
            'items.min' => trans('refund::refund.validation.items_min'),
            'items.*.order_product_id.required' => trans('refund::refund.validation.order_product_required'),
            'items.*.order_product_id.exists' => trans('refund::refund.validation.order_product_invalid'),
            'items.*.quantity.required' => trans('refund::refund.validation.quantity_required'),
            'items.*.quantity.integer' => trans('refund::refund.validation.quantity_integer'),
            'items.*.quantity.min' => trans('refund::refund.validation.quantity_min', ['min' => 1]),
            'items.*.reason.max' => trans('refund::refund.validation.item_reason_max', ['max' => 500]),
        ];
    }

    /**
     * Handle a failed validation attempt (API format).
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
