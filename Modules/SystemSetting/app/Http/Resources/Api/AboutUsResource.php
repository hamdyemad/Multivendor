<?php

namespace Modules\SystemSetting\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutUsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        
        return [
            'section_1' => [
                'image' => $this->section_1_image ? asset('storage/' . $this->section_1_image) : null,
                'title' => $this->getTranslation('section_1_title', $locale) ?? '',
                'text' => $this->getTranslation('section_1_text', $locale) ?? '',
                'sub_section_1' => [
                    'icon' => $this->section_1_sub_section_1_icon ? asset('storage/' . $this->section_1_sub_section_1_icon) : null,
                    'subtitle' => $this->getTranslation('section_1_sub_section_1_subtitle', $locale) ?? '',
                    'text' => $this->getTranslation('section_1_sub_section_1_text', $locale) ?? '',
                ],
                'sub_section_2' => [
                    'icon' => $this->section_1_sub_section_2_icon ? asset('storage/' . $this->section_1_sub_section_2_icon) : null,
                    'subtitle' => $this->getTranslation('section_1_sub_section_2_subtitle', $locale) ?? '',
                    'text' => $this->getTranslation('section_1_sub_section_2_text', $locale) ?? '',
                ],
                'bullets' => [
                    'bullet_1' => $this->getTranslation('section_1_bullet_1', $locale) ?? '',
                    'bullet_2' => $this->getTranslation('section_1_bullet_2', $locale) ?? '',
                ],
                'link' => $this->section_1_link ?? '',
            ],
            'section_2' => [
                'image' => $this->section_2_image ? asset('storage/' . $this->section_2_image) : null,
                'title' => $this->getTranslation('section_2_title', $locale) ?? '',
                'text' => $this->getTranslation('section_2_text', $locale) ?? '',
                'sub_section' => [
                    'subtitle' => $this->getTranslation('section_2_sub_section_1_subtitle', $locale) ?? '',
                    'text' => $this->getTranslation('section_2_sub_section_1_text', $locale) ?? '',
                ],
                'bullets' => [
                    'bullet_1' => $this->getTranslation('section_2_bullet_1', $locale) ?? '',
                    'bullet_2' => $this->getTranslation('section_2_bullet_2', $locale) ?? '',
                    'bullet_3' => $this->getTranslation('section_2_bullet_3', $locale) ?? '',
                    'bullet_4' => $this->getTranslation('section_2_bullet_4', $locale) ?? '',
                ],
                'video_link' => $this->section_2_video_link ?? '',
            ],
            'section_3' => [
                'side_title_1' => $this->getTranslation('section_3_title', $locale) ?? '',
                'side_text_1' => $this->getTranslation('section_3_text', $locale) ?? '',
                'side_title_2' => $this->getTranslation('section_3_sub_section_1_subtitle', $locale) ?? '',
                'side_text_2' => $this->getTranslation('section_3_sub_section_1_text', $locale) ?? '',
                'side_title_3' => $this->getTranslation('section_3_sub_section_2_subtitle', $locale) ?? '',
                'side_text_3' => $this->getTranslation('section_3_sub_section_2_text', $locale) ?? '',
            ],
            'section_4' => [
                'title' => $this->getTranslation('section_4_title', $locale) ?? '',
                'text' => $this->getTranslation('section_4_text', $locale) ?? '',
            ],
            'about' => [
                'bullet_1' => $this->getTranslation('section_3_bullet_1', $locale) ?? '',
                'bullet_2' => $this->getTranslation('section_3_bullet_2', $locale) ?? '',
                'bullet_3' => $this->getTranslation('section_3_bullet_3', $locale) ?? '',
            ],
            'objective' => [
                'bullet_1' => $this->getTranslation('section_3_bullet_4', $locale) ?? '',
                'bullet_2' => $this->getTranslation('section_4_bullet_1', $locale) ?? '',
                'bullet_3' => $this->getTranslation('section_4_bullet_2', $locale) ?? '',
            ],
            'excellent' => [
                'bullet_1' => $this->getTranslation('section_4_bullet_3', $locale) ?? '',
                'bullet_2' => $this->getTranslation('section_4_bullet_4', $locale) ?? '',
                'bullet_3' => $this->getTranslation('section_4_sub_section_1_subtitle', $locale) ?? '',
            ],
            'meta_description' => $this->getTranslation('section_4_sub_section_1_text', $locale) ?? '',
            'meta_keywords' => [],
        ];
    }
}
