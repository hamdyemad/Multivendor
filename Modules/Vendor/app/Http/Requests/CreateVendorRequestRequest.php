<?php

namespace Modules\Vendor\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateVendorRequestRequest extends FormRequest
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
        return [
            'email' => 'required|email|unique:vendor_requests,email',
            'phone' => 'required|string|min:10|max:20',
            'company_name' => 'required|string|min:3|max:255',
            'manager_name' => 'required|string|min:3|max:255',
            'company_logo' => 'required|image',
        ];
    }
}
