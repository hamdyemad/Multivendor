<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\AreaSettings\app\Models\Country;

class CountryObserver
{
    /**
     * Models to exclude from country_id storage
     */
    private array $excludedModels = [
        'App\Models\PersonalAccessToken',
        'App\Models\PasswordReset',
        'App\Models\FailedJob',
    ];


    public function saving(Model $model): void
    {
        $this->storeCountryId($model);
    }

    /**
     * Store country_id from session country_code for models that need it
     */
    public function storeCountryId(Model $model): void
    {
        // Skip if model is excluded
        if ($this->isExcluded($model)) {
            return;
        }

        // Skip if model doesn't have country_id column
        if (!Schema::hasColumn($model->getTable(), 'country_id')) {
            return;
        }

        // Skip if country_id is already set
        if ($model->country_id !== null) {
            return;
        }

        try {
            $country = null;

            // Try to get country from session first
            $countryCode = session('country_code');

            if ($countryCode) {
                $country = Country::where('code', strtoupper($countryCode))->first();
            }

            // If no country from session, try to get default country
            if (!$country) {
                $country = Country::where('default', true)->first();
            }

            // If still no country, get first active country
            if (!$country) {
                $country = Country::where('active', true)->first();
            }

            // Set country_id if found
            if ($country) {
                $model->country_id = $country->id;
            }
        } catch (\Exception $e) {
            // Log::warning("Could not store country_id: " . $e->getMessage());
        }
    }

    /**
     * Check if model is excluded from country_id storage
     */
    private function isExcluded(Model $model): bool
    {
        foreach ($this->excludedModels as $excludedModel) {
            if ($model instanceof $excludedModel || get_class($model) === $excludedModel) {
                return true;
            }
        }
        return false;
    }
}
