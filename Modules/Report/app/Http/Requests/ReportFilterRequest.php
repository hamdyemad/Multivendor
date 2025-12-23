<?php

namespace Modules\Report\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d',
            'from_date' => 'nullable|date_format:Y-m-d',
            'to_date' => 'nullable|date_format:Y-m-d',
            'search' => 'nullable|string|max:500',
            'status' => 'nullable|string|in:active,inactive,approved,pending,rejected',
            'gender' => 'nullable|string|in:male,female,other',
            'type' => 'nullable|string|max:255',
            'city_id' => 'nullable|integer',
            'category' => 'nullable|integer',
            'vendor' => 'nullable|integer',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
