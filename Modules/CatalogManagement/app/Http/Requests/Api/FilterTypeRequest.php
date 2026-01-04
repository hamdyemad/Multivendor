<?php

namespace Modules\CatalogManagement\app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class FilterTypeRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:occasion,bundle',
            'occasion_id' => 'nullable|exists:occasions,id',
            'bundle_category_id' => 'nullable|exists:bundle_categories,id',
            'brand_id' => 'nullable|string', // Accept both ID and slug
        ];
    }

}
