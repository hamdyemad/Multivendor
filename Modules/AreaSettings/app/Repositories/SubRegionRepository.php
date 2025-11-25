<?php

namespace Modules\AreaSettings\app\Repositories;

use Modules\AreaSettings\app\Interfaces\SubRegionRepositoryInterface;
use Modules\AreaSettings\app\Models\SubRegion;
use Illuminate\Support\Facades\DB;

class SubRegionRepository implements SubRegionRepositoryInterface
{
    /**
     * Get all subregions with filters and pagination
     */
    public function getAllSubRegions(array $filters = [], int $perPage = 15)
    {
        $query = SubRegion::with(['region', 'translations']);

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('translations', function($q) use ($search) {
                $q->where('lang_value', 'like', "%{$search}%");
            });
        }

        // Region filter
        if (!empty($filters['region_id'])) {
            $query->where('region_id', $filters['region_id']);
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
     * Get subregions query for DataTables
     */
    public function getSubRegionsQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        $query = SubRegion::with(['region.translations', 'translations']);

        // Debug: Log initial query
        \Log::info('SubRegion Query - Start', ['filters' => $filters]);

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            \Log::info('SubRegion Query - Applying search filter', ['search' => $search]);
            $query->whereHas('translations', function($q) use ($search) {
                $q->where('lang_key', 'name')
                  ->where('lang_value', 'like', "%{$search}%");
            });
        }

        // Region filter
        if (!empty($filters['region_id'])) {
            \Log::info('SubRegion Query - Applying region filter', ['region_id' => $filters['region_id']]);
            $query->where('region_id', $filters['region_id']);
        }

        // Active filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            \Log::info('SubRegion Query - Applying active filter', ['active' => $filters['active']]);
            $query->where('active', $filters['active']);
        }

        // Date from filter
        if (!empty($filters['created_date_from'])) {
            \Log::info('SubRegion Query - Applying date from filter', ['date_from' => $filters['created_date_from']]);
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        // Date to filter
        if (!empty($filters['created_date_to'])) {
            \Log::info('SubRegion Query - Applying date to filter', ['date_to' => $filters['created_date_to']]);
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        // Apply sorting
        if ($orderBy !== null) {
            if (is_array($orderBy)) {
                // Sorting by translated name
                $langId = $orderBy['lang_id'];
                $query->leftJoin('translations as t_sort', function($join) use ($langId) {
                    $join->on('subregions.id', '=', 't_sort.translatable_id')
                         ->where('t_sort.translatable_type', '=', 'Modules\\AreaSettings\\app\\Models\\SubRegion')
                         ->where('t_sort.lang_id', '=', $langId)
                         ->where('t_sort.lang_key', '=', 'name');
                })
                ->orderBy('t_sort.lang_value', $orderDirection)
                ->select('subregions.*');
            } else {
                // Sorting by regular column
                $query->orderBy($orderBy, $orderDirection);
            }
        }

        // Debug: Log final SQL query
        \Log::info('SubRegion Query - Final SQL', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        return $query;
    }

    /**
     * Get subregion by ID
     */
    public function getSubRegionById(int $id)
    {
        return SubRegion::with(['region', 'translations'])->findOrFail($id);
    }

    /**
     * Create a new subregion
     */
    public function createSubRegion(array $data)
    {
        return DB::transaction(function () use ($data) {
            $subRegion = SubRegion::create([
                'region_id' => $data['region_id'],
                'active' => $data['active'] ?? 0,
            ]);

            // Set translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $subRegion->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'name',
                            'lang_value' => $translation['name'],
                        ]);
                    }
                }
            }

            return $subRegion;
        });
    }

    /**
     * Update subregion
     */
    public function updateSubRegion(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $subRegion = SubRegion::findOrFail($id);
            $updatedData = [];
            (isset($data['region_id'])) ? $updatedData['region_id'] = $data['region_id'] : null;
            if(isset($data['active'])) {
                if($data['active'] == 1) {
                    $updatedData['active'] = 1;
                } else {
                    $updatedData['active'] = 0;
                }
            }
            $subRegion->update($updatedData);

            // Update translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $subRegion->translations()->updateOrCreate(
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

            $subRegion->refresh();
            $subRegion->load(['region', 'translations']);

            return $subRegion;
        });
    }

    /**
     * Delete subregion
     */
    public function deleteSubRegion(int $id)
    {
        $subRegion = SubRegion::findOrFail($id);
        $subRegion->translations()->delete();
        return $subRegion->delete();
    }

    /**
     * Get active subregions
     */
    public function getActiveSubRegions()
    {
        return SubRegion::with(['region', 'translations'])->where('active', 1)
            ->get();
    }

    /**
     * Get subregions by region
     */
    public function getSubRegionsByRegion(int $regionId)
    {
        return SubRegion::with(['region', 'translations'])
            ->where('region_id', $regionId)
            ->where('active', 1)
            ->get();
    }
}
