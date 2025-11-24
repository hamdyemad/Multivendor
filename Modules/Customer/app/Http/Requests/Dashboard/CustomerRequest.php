<?php

namespace Modules\Customer\app\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $customerId = $this->route('customer') ?? $this->route('id');
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH') || !empty($customerId);

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('customers', 'email')->ignore($customerId)->whereNull('deleted_at'),
            ],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'password' => $isUpdate ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed',
            'status' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => __('customer::customer.first_name') . ' ' . __('validation.required'),
            'first_name.string' => __('customer::customer.first_name') . ' ' . __('validation.string'),
            'first_name.max' => __('customer::customer.first_name') . ' ' . __('validation.max.string', ['max' => 255]),

            'last_name.required' => __('customer::customer.last_name') . ' ' . __('validation.required'),
            'last_name.string' => __('customer::customer.last_name') . ' ' . __('validation.string'),
            'last_name.max' => __('customer::customer.last_name') . ' ' . __('validation.max.string', ['max' => 255]),

            'email.required' => __('customer::customer.email') . ' ' . __('validation.required'),
            'email.email' => __('customer::customer.email') . ' ' . __('validation.email'),
            'email.unique' => __('customer::customer.email') . ' ' . __('validation.unique'),

            'phone.string' => __('customer::customer.phone') . ' ' . __('validation.string'),
            'phone.max' => __('customer::customer.phone') . ' ' . __('validation.max.string', ['max' => 20]),

            'date_of_birth.date' => __('customer::customer.date_of_birth') . ' ' . __('validation.date'),
            'date_of_birth.before' => __('customer::customer.date_of_birth') . ' ' . __('validation.before', ['date' => 'today']),

            'gender.in' => __('customer::customer.gender') . ' ' . __('validation.in'),

            'password.required' => __('customer::customer.password') . ' ' . __('validation.required'),
            'password.string' => __('customer::customer.password') . ' ' . __('validation.string'),
            'password.min' => __('customer::customer.password') . ' ' . __('validation.min.string', ['min' => 8]),
            'password.confirmed' => __('customer::customer.password') . ' ' . __('validation.confirmed'),

            'status.boolean' => __('customer::customer.status') . ' ' . __('validation.boolean'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => __('customer::customer.first_name'),
            'last_name' => __('customer::customer.last_name'),
            'email' => __('customer::customer.email'),
            'phone' => __('customer::customer.phone'),
            'date_of_birth' => __('customer::customer.date_of_birth'),
            'gender' => __('customer::customer.gender'),
            'password' => __('customer::customer.password'),
            'status' => __('customer::customer.status'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->boolean('status'),
        ]);
    }

    /**
     * Get the validated data from the request.
     *
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Remove password if it's empty (for updates)
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        return $validated;
    }
}
