<?php

namespace Modules\CatalogManagement\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OccasionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'vendor' => [
                'id' => $this->vendor?->id,
                'name' => $this->vendor?->name,
                'slug' => $this->vendor?->slug,
            ],
            'name' => $this->getTranslation('name', app()->getLocale()) ?? '',
            'title' => $this->getTranslation('title', app()->getLocale()) ?? '',
            'sub_title' => $this->getTranslation('sub_title', app()->getLocale()) ?? '',
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'is_active' => $this->is_active,
            'slug' => $this->slug,

            // SEO Information
            'seo_title' => $this->getTranslation('seo_title', app()->getLocale()) ?? '',
            'seo_description' => $this->getTranslation('seo_description', app()->getLocale()) ?? '',
            'seo_keywords' => $this->getTranslation('seo_keywords', app()->getLocale()) ?? '',

            // Image
            'image' => $this->attachments()
                ->where('type', 'image')
                ->first()?->path ? asset('storage/' . $this->attachments()->where('type', 'image')->first()->path) : null,

            // Occasion Products
            'occasion_products' => OccasionProductResource::collection($this->occasionProducts),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get translations for all languages
     */
    private function getTranslationsForAllLanguages()
    {
        $languages = \App\Models\Language::all();
        $translations = [];

        foreach ($languages as $language) {
            $translations[$language->id] = [
                'id' => $language->id,
                'code' => $language->code,
                'name' => $language->name,
                'name' => $this->getTranslation('name', $language->code) ?? '',
                'title' => $this->getTranslation('title', $language->code) ?? '',
                'sub_title' => $this->getTranslation('sub_title', $language->code) ?? '',
                'seo_title' => $this->getTranslation('seo_title', $language->code) ?? '',
                'seo_description' => $this->getTranslation('seo_description', $language->code) ?? '',
                'seo_keywords' => $this->getTranslation('seo_keywords', $language->code) ?? '',
            ];
        }

        return $translations;
    }
}
