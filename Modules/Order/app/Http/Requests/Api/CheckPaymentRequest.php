<?php

namespace Modules\Order\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CheckPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paymob_order_id' => 'required|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'paymob_order_id' => $this->route('paymobOrderId'),
        ]);
    }

    public function messages(): array
    {
        return [
            'paymob_order_id.required' => __('validation.required', ['attribute' => 'paymob order id']),
        ];
    }
}
