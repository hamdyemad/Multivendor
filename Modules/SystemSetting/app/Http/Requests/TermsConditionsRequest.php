<?php

namespace Modules\SystemSetting\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TermsConditionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|array',
            'title.*' => 'nullable|array',
            'title.*.*' => 'nullable|string',
            'description' => 'nullable|array',
            'description.*' => 'nullable|array',
            'description.*.*' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'title_en' => __('systemsetting::terms-conditions.title_en'),
            'title_ar' => __('systemsetting::terms-conditions.title_ar'),
            'description_en' => __('systemsetting::terms-conditions.description_en'),
            'description_ar' => __('systemsetting::terms-conditions.description_ar'),
        ];
    }
}
