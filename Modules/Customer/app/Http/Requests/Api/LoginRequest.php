<?php

namespace Modules\Customer\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', Rule::exists('customers', 'email')->whereNotNull('email_verified_at')],
            'password' => 'required|string',
            'fcm_token' => 'nullable|string',
            'device_id' => 'nullable|string|uuid',
        ];
    }
}
