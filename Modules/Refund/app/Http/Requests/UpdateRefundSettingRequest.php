<?php

namespace Modules\Refund\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRefundSettingRequest extends FormRequest
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
            'customer_pays_return_shipping' => 'required|boolean',
            'refund_processing_days' => 'required|integer|min:1|max:365',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'customer_pays_return_shipping' => trans('refund::refund.fields.customer_pays_return_shipping'),
            'refund_processing_days' => trans('refund::refund.fields.refund_processing_days'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer_pays_return_shipping.required' => trans('validation.required', ['attribute' => trans('refund::refund.fields.customer_pays_return_shipping')]),
            'customer_pays_return_shipping.boolean' => trans('validation.boolean', ['attribute' => trans('refund::refund.fields.customer_pays_return_shipping')]),
            'refund_processing_days.required' => trans('validation.required', ['attribute' => trans('refund::refund.fields.refund_processing_days')]),
            'refund_processing_days.integer' => trans('validation.integer', ['attribute' => trans('refund::refund.fields.refund_processing_days')]),
            'refund_processing_days.min' => trans('validation.min.numeric', ['attribute' => trans('refund::refund.fields.refund_processing_days'), 'min' => 1]),
            'refund_processing_days.max' => trans('validation.max.numeric', ['attribute' => trans('refund::refund.fields.refund_processing_days'), 'max' => 365]),
        ];
    }
}
