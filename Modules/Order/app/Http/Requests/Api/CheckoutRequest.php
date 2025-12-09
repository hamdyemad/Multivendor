<?php

namespace Modules\Order\app\Http\Requests\Api;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
            'customer_address_id' => 'required|integer|exists:customer_addresses,id',
            'promo_code_id' => 'nullable|exists:promocodes,id',
            'order_from' => Rule::in(['WEB', 'ANDROID', 'IOS', 'web', 'android', 'ios']),
            'payment_type' => Rule::in(['cash_on_delivery', 'online', 'aman']),
            'use_point' => 'required|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_address_id.required' => __('validation.user_address_id_required'),
            'user_address_id.integer' => __('validation.user_address_id_integer'),
            'user_address_id.exists' => __('validation.user_address_id_exists'),
            'promo_code_id.exists' => __('validation.promo_code_id_exists'),
            'order_from.in' => __('validation.order_from_in'),
            'payment_type.in' => __('validation.payment_type_in'),
            'use_point.required' => __('validation.use_point_required'),
            'use_point.boolean' => __('validation.use_point_boolean'),
        ];
    }
}
