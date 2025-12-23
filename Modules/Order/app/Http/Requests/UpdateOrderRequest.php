<?php

namespace Modules\Order\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'customer_type' => 'required|in:existing,external',
            'selected_customer_id' => 'required_if:customer_type,existing|nullable|exists:customers,id',
            'customer_address_id' => 'nullable|exists:customer_addresses,id',
            'external_customer_name' => 'required_if:customer_type,external|nullable|string|max:255',
            'external_customer_email' => 'required_if:customer_type,external|nullable|email|max:255',
            'external_customer_phone' => 'required_if:customer_type,external|nullable|string|max:20',
            'external_customer_address' => 'required_if:customer_type,external|nullable|string',
            'products' => 'required|json',
            'feesData' => 'nullable|json',
            'discountsData' => 'nullable|json',
            'shipping' => 'nullable|numeric|min:0',
        ];
    }
}
