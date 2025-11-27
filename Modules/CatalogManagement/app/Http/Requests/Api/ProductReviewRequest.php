<?php

namespace Modules\CatalogManagement\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ProductReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'rating' => 'required|integer|min:1|max:5',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => __('validation.required', ['attribute' => __('fields.title')]),
            'title.max' => __('validation.max.string', ['attribute' => __('fields.title'), 'max' => 255]),
            'content.required' => __('validation.required', ['attribute' => __('fields.content')]),
            'content.max' => __('validation.max.string', ['attribute' => __('fields.content'), 'max' => 5000]),
            'rating.required' => __('validation.required', ['attribute' => __('fields.rating')]),
            'rating.min' => __('validation.min.numeric', ['attribute' => __('fields.rating'), 'min' => 1]),
            'rating.max' => __('validation.max.numeric', ['attribute' => __('fields.rating'), 'max' => 5]),
        ];
    }
}
