<?php

namespace Modules\Customer\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255|unique:subscriptions,email,NULL,id,deleted_at,NULL',
            'customer_id' => 'nullable|exists:customers,id',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
