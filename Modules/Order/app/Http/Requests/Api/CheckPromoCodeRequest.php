<?php

namespace Modules\Order\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CheckPromoCodeRequest extends FormRequest
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
            'code' => 'required|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.required' => __('order::validation.promo_code_required'),
            'code.max' => __('order::validation.promo_code_max'),
        ];
    }
}
