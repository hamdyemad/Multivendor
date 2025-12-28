<?php

namespace Modules\Order\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
        // dd($this->all());
        return [
            // Customer Information
            'customer_type' => 'required|in:existing,external',
            'selected_customer_id' => 'nullable|required_if:customer_type,existing|exists:customers,id',
            'external_customer_name' => 'nullable|required_if:customer_type,external|string|max:255',
            'external_customer_email' => 'nullable|required_if:customer_type,external|email|max:255',
            'external_customer_phone' => 'nullable|required_if:customer_type,external|string|max:20',
            'external_customer_address' => 'nullable|required_if:customer_type,external|string',
            'external_city_id' => 'nullable|required_if:customer_type,external|exists:cities,id',
            'external_region_id' => 'nullable|required_if:customer_type,external|exists:regions,id',
            'customer_address_id' => 'nullable|required_if:customer_type,existing|exists:customer_addresses,id',

            // Order Details
            'shipping' => 'required|numeric|min:0',

            // Products
            'products' => 'required|array|min:1',

            // Fees and Discounts
            'feesData' => 'nullable|array',
            'discountsData' => 'nullable|array',

            // Request Quotation
            'quotation_id' => 'nullable|exists:request_quotations,id',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'selected_customer_id.required_if' => trans('order::order.customer_required'),
            'external_customer_name.required_if' => trans('order::order.customer_name_required'),
            'external_customer_email.required_if' => trans('order::order.customer_email_required'),
            'external_customer_phone.required_if' => trans('order::order.customer_phone_required'),
            'external_customer_address.required_if' => trans('order::order.customer_address_required'),
            'external_city_id.required_if' => trans('order::order.city_required'),
            'external_region_id.required_if' => trans('order::order.region_required'),
            'customer_address_id.required_if' => trans('order::order.address_required'),
            'products.required' => trans('order::order.products_required'),
            'products.min' => trans('order::order.products_required'),
        ];
    }

    /**
     * Prepare the data for validation.
     * Convert JSON strings to arrays if needed.
     */
    protected function prepareForValidation(): void
    {
        // Parse JSON products if it's a string
        if (is_string($this->products)) {
            $this->merge([
                'products' => json_decode($this->products, true) ?? [],
            ]);
        }

        // Parse JSON fees if it's a string, or set to empty array if missing
        if (is_string($this->feesData)) {
            $this->merge([
                'feesData' => json_decode($this->feesData, true) ?? [],
            ]);
        } elseif (!$this->has('feesData')) {
            $this->merge([
                'feesData' => [],
            ]);
        }

        // Parse JSON discounts if it's a string, or set to empty array if missing
        if (is_string($this->discountsData)) {
            $this->merge([
                'discountsData' => json_decode($this->discountsData, true) ?? [],
            ]);
        } elseif (!$this->has('discountsData')) {
            $this->merge([
                'discountsData' => [],
            ]);
        }
    }
}
