<?php

namespace Modules\Refund\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRefundNotesRequest extends FormRequest
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
            'notes' => 'required|string|max:1000',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'notes' => trans('common.notes'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'notes.required' => trans('validation.required', ['attribute' => trans('common.notes')]),
            'notes.max' => trans('validation.max.string', ['attribute' => trans('common.notes'), 'max' => 1000]),
        ];
    }
}
