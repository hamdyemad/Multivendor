<?php

namespace Modules\SystemSetting\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\SystemSetting\app\Models\AboutUs;

class AboutUsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'translations' => 'nullable|array',
            // Link fields (not translatable)
            'section_1_link' => 'nullable|string|max:500',
            'section_2_video_link' => 'nullable|string|max:500',
        ];
        
        // Add rules for each section (1-4)
        for ($i = 1; $i <= 4; $i++) {
            // Image fields
            $rules["section_{$i}_image"] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120';
            $rules["section_{$i}_sub_section_1_icon"] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
            $rules["section_{$i}_sub_section_2_icon"] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
            
            // Translation fields
            $rules["translations.*.section_{$i}_title"] = 'nullable|string|max:255';
            $rules["translations.*.section_{$i}_text"] = 'nullable|string';
            $rules["translations.*.section_{$i}_sub_section_1_subtitle"] = 'nullable|string|max:255';
            $rules["translations.*.section_{$i}_sub_section_1_text"] = 'nullable|string';
            $rules["translations.*.section_{$i}_sub_section_2_subtitle"] = 'nullable|string|max:255';
            $rules["translations.*.section_{$i}_sub_section_2_text"] = 'nullable|string';
            $rules["translations.*.section_{$i}_bullet_1"] = 'nullable|string|max:500';
            $rules["translations.*.section_{$i}_bullet_2"] = 'nullable|string|max:500';
            $rules["translations.*.section_{$i}_bullet_3"] = 'nullable|string|max:500';
            $rules["translations.*.section_{$i}_bullet_4"] = 'nullable|string|max:500';
        }
        
        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [];
        
        for ($i = 1; $i <= 4; $i++) {
            $attributes["section_{$i}_image"] = __('systemsetting::about-us.section_image') . " {$i}";
            $attributes["section_{$i}_sub_section_1_icon"] = __('systemsetting::about-us.icon') . " {$i}.1";
            $attributes["section_{$i}_sub_section_2_icon"] = __('systemsetting::about-us.icon') . " {$i}.2";
        }
        
        return $attributes;
    }
}
