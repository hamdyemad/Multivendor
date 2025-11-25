<?php

namespace Modules\AreaSettings\app\Repositories;

use Modules\AreaSettings\app\Interfaces\CityRepositoryInterface;
use Modules\AreaSettings\app\Models\City;
use Illuminate\Support\Facades\DB;

class CityRepository implements CityRepositoryInterface
{
    /**
     * Get all cities with filters and pagination
     */
    public function getAllCities(array $filters = [], int $perPage = 15)
    {
        $query = City::with(['country.translations', 'translations']);

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('translations', function($q) use ($search) {
                $q->where('lang_value', 'like', "%{$search}%");
            });
        }

        // Country filter
        if (!empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        // Active filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        // Date from filter
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        // Date to filter
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Return paginated or all records
        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get cities query for DataTables
     */
    public function getCitiesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        $query = City::with(['country.translations', 'translations']);

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('translations', function($q) use ($search) {
                $q->where('lang_value', 'like', "%{$search}%");
            });
        }

        // Country filter
        if (!empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        // Active filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        // Date from filter
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        // Date to filter
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        // Apply sorting
        if ($orderBy !== null) {
            if (is_array($orderBy)) {
                // Sorting by translated name
                $langId = $orderBy['lang_id'];
                $query->leftJoin('translations as t_sort', function($join) use ($langId) {
                    $join->on('cities.id', '=', 't_sort.translatable_id')
                         ->where('t_sort.translatable_type', '=', 'Modules\\AreaSettings\\Models\\City')
                         ->where('t_sort.lang_id', '=', $langId)
                         ->where('t_sort.lang_key', '=', 'name');
                })
                ->orderBy('t_sort.lang_value', $orderDirection)
                ->select('cities.*');
            } else {
                // Sorting by regular column
                $query->orderBy($orderBy, $orderDirection);
            }
        }

        return $query;
    }

    /**
     * Get city by ID
     */
    public function getCityById(int $id)
    {
        return City::with(['country.translations', 'translations'])->findOrFail($id);
    }

    /**
     * Create a new city
     */
    public function createCity(array $data)
    {
        return DB::transaction(function () use ($data) {
            $city = City::create([
                'country_id' => $data['country_id'],
                'active' => $data['active'] ?? 0,
            ]);

            // Set translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $city->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'name',
                            'lang_value' => $translation['name'],
                        ]);
                    }
                }
            }

            return $city;
        });
    }

    /**
     * Update city
     */
    public function updateCity(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $city = City::findOrFail($id);
            $updatedData = [];
            (isset($data['country_id'])) ? $updatedData['country_id'] = $data['country_id'] : null;
            if(isset($data['active'])) {
                if($data['active'] == 1) {
                    $updatedData['active'] = 1;
                } else {
                    $updatedData['active'] = 0;
                }
            }
            $city->update($updatedData);

            // Update translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $city->translations()->updateOrCreate(
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

            $city->refresh();
            $city->load(['country.translations', 'translations']);

            return $city;
        });
    }

    /**
     * Delete city
     */
    public function deleteCity(int $id)
    {
        $city = City::findOrFail($id);
        $city->translations()->delete();
        return $city->delete();
    }

    /**
     * Get active cities
     */
    public function getActiveCities()
    {
        return City::with(['country.translations', 'translations'])->where('active', 1)
            ->get();
    }

    /**
     * Get cities by country
     */
    public function getCitiesByCountry(int $countryId)
    {
        return City::with(['country.translations', 'translations'])
            ->where('country_id', $countryId)
            ->where('active', 1)
            ->get();
    }
}
