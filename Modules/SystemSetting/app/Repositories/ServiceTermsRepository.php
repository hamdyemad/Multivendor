<?php

namespace Modules\SystemSetting\app\Repositories;

use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Modules\SystemSetting\app\Models\ServiceTerms;

class ServiceTermsRepository
{
    public function getOrCreate()
    {
        return ServiceTerms::first() ?? ServiceTerms::create([]);
    }

    public function update($data)
    {
        return DB::transaction(function () use ($data) {
            $terms = $this->getOrCreate();
            \Log::info($data);
            // Handle multilingual description from x-multilingual-input component
            if (isset($data['description']) && is_array($data['description'])) {
                $languages = Language::all()->keyBy('id');

                foreach ($data['description'] as $languageId => $translations) {
                    if (is_array($translations) && isset($languages[$languageId])) {
                        $languageCode = $languages[$languageId]->code;
                        $descriptionValue = $translations['description'] ?? '';
                        // Only save if value is not empty
                        if (!empty(trim($descriptionValue))) {
                            $terms->setTranslation('description', $languageCode, (string)$descriptionValue);
                        }
                    }
                }
                $terms->refresh();
            }

            return $terms;
        });
    }

    public function getServiceTerms()
    {
        return $this->getOrCreate();
    }
}
