<?php

namespace Modules\SystemSetting\app\Repositories;

use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\SystemSetting\app\Models\AboutUs;

class AboutUsRepository
{
    /**
     * Get or create about us for a platform
     */
    public function getOrCreate(string $platform = 'website')
    {
        return AboutUs::platform($platform)->first() ?? AboutUs::create(['platform' => $platform]);
    }

    /**
     * Get about us by platform
     */
    public function getByPlatform(string $platform = 'website')
    {
        return $this->getOrCreate($platform);
    }

    /**
     * Update about us
     */
    public function update(array $data, string $platform = 'website')
    {
        return DB::transaction(function () use ($data, $platform) {
            $aboutUs = $this->getOrCreate($platform);

            // Handle image uploads for each section
            $imageFields = AboutUs::getImageFields();

            foreach ($imageFields as $field) {
                if (isset($data[$field]) && $data[$field] instanceof \Illuminate\Http\UploadedFile) {
                    // Delete old image if exists
                    if ($aboutUs->$field) {
                        Storage::disk('public')->delete($aboutUs->$field);
                    }
                    $data[$field] = $data[$field]->store('about-us', 'public');
                } else {
                    unset($data[$field]);
                }
            }

            // Update non-translatable fields (image fields + link fields)
            $linkFields = AboutUs::getLinkFields();
            $updateData = array_intersect_key($data, array_flip(array_merge($imageFields, $linkFields)));

            if (!empty($updateData)) {
                $aboutUs->update($updateData);
            }

            // Store translations
            $this->storeTranslations($aboutUs, $data);

            return $aboutUs->fresh();
        });
    }

    /**
     * Store translations for about us
     */
    protected function storeTranslations(AboutUs $aboutUs, array $data): void
    {
        if (!isset($data['translations']) || !is_array($data['translations'])) {
            return;
        }

        $languages = Language::all();
        $translatableFields = AboutUs::getTranslatableFields();

        foreach ($languages as $language) {
            $translationData = $data['translations'][$language->id] ?? [];

            foreach ($translatableFields as $field) {
                if (isset($translationData[$field])) {
                    $value = $translationData[$field];
                    if ($value !== null && trim((string)$value) !== '') {
                        $aboutUs->translations()->updateOrCreate(
                            [
                                'lang_id' => $language->id,
                                'lang_key' => $field,
                            ],
                            [
                                'lang_value' => $value,
                            ]
                        );
                    }
                }
            }
        }
    }
}
