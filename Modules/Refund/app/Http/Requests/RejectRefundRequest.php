<?php

namespace Modules\Refund\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectRefundRequest extends FormRequest
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
            'rejection_reason' => 'required|string|max:1000',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'rejection_reason' => trans('refund::refund.fields.rejection_reason'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'rejection_reason.required' => trans('validation.required', ['attribute' => trans('refund::refund.fields.rejection_reason')]),
            'rejection_reason.max' => trans('validation.max.string', ['attribute' => trans('refund::refund.fields.rejection_reason'), 'max' => 1000]),
        ];
    }
}
