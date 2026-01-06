<?php

namespace Modules\AreaSettings\app\Repositories;

use Modules\AreaSettings\app\Interfaces\CountryRepositoryInterface;
use Modules\AreaSettings\app\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        Log::info('CountryRepository filters:', $filters);

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
            // If this country is being set as default, unset all other defaults
            if (isset($data['default']) && $data['default'] == 1) {
                Country::where('default', 1)->update(['default' => 0]);
            }

            $country = Country::create([
                'code' => $data['code'],
                'phone_code' => $data['phone_code'] ?? null,
                'phone_length' => $data['phone_length'] ?? null,
                'currency_id' => $data['currency_id'],
                'active' => $data['active'] ?? 0,
                'default' => $data['default'] ?? 0,
            ]);

            // Handle image upload
            if (isset($data['image'])) {
                $path = $data['image']->store("countries/$country->id", 'public');
                $country->attachments()->create([
                    'path' => $path,
                    'type' => 'image'
                ]);
            }

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

            // If this country is being set as default, unset all other defaults
            if (isset($data['default']) && $data['default'] == 1) {
                Country::where('id', '!=', $id)->where('default', 1)->update(['default' => 0]);
            }

            $updatedData = [];
            (isset($data['code'])) ? $updatedData['code'] = $data['code'] : null;
            (isset($data['phone_code'])) ? $updatedData['phone_code'] = $data['phone_code'] : null;
            (isset($data['phone_length'])) ? $updatedData['phone_length'] = $data['phone_length'] : null;
            (isset($data['currency_id'])) ? $updatedData['currency_id'] = $data['currency_id'] : null;
            if(isset($data['active'])) {
                if($data['active'] == 1) {
                    $updatedData['active'] = 1;
                } else {
                    $updatedData['active'] = 0;
                }
            }
            if(isset($data['default'])) {
                if($data['default'] == 1) {
                    $updatedData['default'] = 1;
                } else {
                    $updatedData['default'] = 0;
                }
            }
            $country->update($updatedData);


            // Handle image upload
            if (isset($data['image'])) {
                $country->attachments()->delete();
                $image = $country->attachments()->first();
                if(file_exists($image)) {
                    unlink($image->path);
                }
                $path = $data['image']->store("countries/$country->id", 'public');
                $country->attachments()->create([
                    'path' => $path,
                    'type' => 'image'
                ]);
            }


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

        $country->attachments()->forceDelete();
        $image = $country->attachments()->first();
        if(file_exists($image)) {
            unlink($image->path);
        }

        // Check if this is the last country - cannot delete
        $totalCountriesCount = Country::count();
        if ($totalCountriesCount <= 1) {
            throw new \Exception(
                __('areasettings::country.cannot_delete_last_country')
            );
        }

        // Check if country has cities
        $citiesCount = $country->cities()->count();
        if ($citiesCount > 0) {
            throw new \Exception(
                __('areasettings::country.cannot_delete_country_with_cities', [
                    'count' => $citiesCount
                ])
            );
        }

        // If this country is the default, transfer default to another country
        if ($country->default) {
            $newDefaultCountry = Country::where('id', '!=', $id)->first();
            if ($newDefaultCountry) {
                $newDefaultCountry->default = 1;
                $newDefaultCountry->save();
            }
        }

        $country->translations()->forceDelete();
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
