<?php

namespace Modules\Order\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\Payment;

class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', Rule::exists('orders', 'id')],
            'method' => ['required', 'in:card,souhola,valu,forsa,wallet,bank_installment'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $orderId = $this->input('order_id');
            if (!$orderId) return;

            $order = Order::find($orderId);
            if (!$order) return;

            if ($order->payment_type !== 'online') {
                $validator->errors()->add('order_id', __('order::order.payment_type_not_online'));
            }


            $existingPayment = Payment::where('order_id', $orderId)
                ->where('status', Payment::STATUS_PAID)
                ->first();

            if ($existingPayment) {
                $validator->errors()->add('order_id', __('order::order.order_already_paid'));
            }
        });
    }

    public function messages(): array
    {
        return [
            'order_id.required' => __('validation.required', ['attribute' => 'order']),
            'order_id.exists' => __('validation.exists', ['attribute' => 'order']),
            'method.required' => __('validation.required', ['attribute' => 'payment method']),
            'method.in' => __('validation.in', ['attribute' => 'payment method']),
        ];
    }
}
