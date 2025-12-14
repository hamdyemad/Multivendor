<?php

namespace Modules\SystemSetting\app\Repositories;

use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Modules\SystemSetting\app\Models\PrivacyPolicy;

class PrivacyPolicyRepository
{
    public function getOrCreate()
    {
        return PrivacyPolicy::first() ?? PrivacyPolicy::create([]);
    }

    public function update($data)
    {
        return DB::transaction(function () use ($data) {
            $policy = $this->getOrCreate();
            // Handle multilingual description from x-multilingual-input component
            if (isset($data['description']) && is_array($data['description'])) {
                $languages = Language::all()->keyBy('id');

                foreach ($data['description'] as $languageId => $translations) {
                    if (is_array($translations) && isset($languages[$languageId])) {
                        $languageCode = $languages[$languageId]->code;
                        // The component sends data as description[languageId][fieldName]
                        // where fieldName is 'description' in this case
                        $descriptionValue = $translations['description'] ?? '';
                        // Always set the translation, even if empty (to clear previous values)
                        $policy->setTranslation('description', $languageCode, (string)$descriptionValue);
                    }
                }
                // Refresh the policy to get the updated translations
                $policy->refresh();
            }

            return $policy;
        });
    }

    public function getPrivacyPolicy()
    {
        return $this->getOrCreate();
    }
}
