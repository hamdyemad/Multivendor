<?php

namespace Modules\AreaSettings\app\Repositories;

use Modules\AreaSettings\app\Interfaces\CountryRepositoryInterface;
use Modules\AreaSettings\app\Models\Country;
use Illuminate\Support\Facades\DB;

class CountryRepository implements CountryRepositoryInterface
{
    /**
     * Get all countries with filters and pagination
     */
    public function getAllCountries(array $filters = [], ?int $perPage = 15)
    {
        $query = $this->getCountriesQuery($filters);
        // Return paginated or all records
        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get countries query for DataTables
     */
    public function getCountriesQuery(array $filters = [])
    {
        $query = Country::with('translations')->filter($filters);

        // Debug: Log filters in repository
        \Log::info('CountryRepository filters:', $filters);

        return $query;
    }

    /**
     * Get country by ID
     */
    public function getCountryById(int $id)
    {
        return Country::with(['translations', 'currency.translations'])->findOrFail($id);
    }

    /**
     * Create a new country
     */
    public function createCountry(array $data)
    {
        return DB::transaction(function () use ($data) {
            $country = Country::create([
                'code' => $data['code'],
                'phone_code' => $data['phone_code'] ?? null,
                'currency_id' => $data['currency_id'],
                'active' => $data['active'] ?? 0,
            ]);

            // Set translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $country->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'name',
                            'lang_value' => $translation['name'],
                        ]);
                    }
                }
            }

            return $country;
        });
    }

    /**
     * Update country
     */
    public function updateCountry(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $country = Country::findOrFail($id);
            $updatedData = [];
            (isset($data['code'])) ? $updatedData['code'] = $data['code'] : null;
            (isset($data['phone_code'])) ? $updatedData['phone_code'] = $data['phone_code'] : null;
            (isset($data['currency_id'])) ? $updatedData['currency_id'] = $data['currency_id'] : null;
            if(isset($data['active'])) {
                if($data['active'] == 1) {
                    $updatedData['active'] = 1;
                } else {
                    $updatedData['active'] = 0;
                }
            }
            $country->update($updatedData);

            // Update translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $country->translations()->updateOrCreate(
                            [
                                'lang_id' => $langId,
                                'lang_key' => 'name',
                            ],
                            [
                                'lang_value' => $translation['name'],
                            ]
                        );
                    }
                }
            }

            $country->refresh();
            $country->load('translations');

            return $country;
        });
    }

    /**
     * Delete country
     */
    public function deleteCountry(int $id)
    {
        $country = Country::findOrFail($id);
        $country->translations()->delete();
        return $country->delete();
    }

    /**
     * Get active countries
     */
    public function getActiveCountries()
    {
        return Country::with('translations')->where('active', 1)
            ->get();
    }
}
