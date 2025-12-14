<?php

namespace Modules\SystemSetting\app\Repositories;

use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Modules\SystemSetting\app\Models\TermsConditions;

class TermsConditionsRepository
{
    public function getOrCreate()
    {
        return TermsConditions::first() ?? TermsConditions::create([]);
    }

    public function update($data)
    {
        return DB::transaction(function () use ($data) {
            $terms = $this->getOrCreate();
            $languages = Language::all()->keyBy('id');

            // Handle multilingual title from x-multilingual-input component
            if (isset($data['title']) && is_array($data['title'])) {
                foreach ($data['title'] as $languageId => $translations) {
                    if (is_array($translations) && isset($languages[$languageId])) {
                        $languageCode = $languages[$languageId]->code;
                        $titleValue = $translations['title'] ?? '';
                        $terms->setTranslation('title', $languageCode, (string)$titleValue);
                    }
                }
            }

            // Handle multilingual description from x-multilingual-input component
            if (isset($data['description']) && is_array($data['description'])) {
                foreach ($data['description'] as $languageId => $translations) {
                    if (is_array($translations) && isset($languages[$languageId])) {
                        $languageCode = $languages[$languageId]->code;
                        $descriptionValue = $translations['description'] ?? '';
                        $terms->setTranslation('description', $languageCode, (string)$descriptionValue);
                    }
                }
            }

            // Refresh the terms to get the updated translations
            $terms->refresh();

            return $terms;
        });
    }

    public function getTermsConditions()
    {
        return $this->getOrCreate();
    }
}
