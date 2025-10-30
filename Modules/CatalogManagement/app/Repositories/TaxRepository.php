<?php

namespace Modules\CatalogManagement\app\Repositories;

use Modules\CatalogManagement\app\Interfaces\TaxRepositoryInterface;
use Modules\CatalogManagement\app\Models\Tax;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TaxRepository implements TaxRepositoryInterface
{
    /**
     * Get all taxes with filters and pagination
     */
    public function getAllTaxes(array $filters = [], int $perPage = 15)
    {
        $query = Tax::with('translations');

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                })
                ->orWhere('tax_rate', 'like', "%{$search}%");
            });
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

        return ($perPage) ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get taxes query for DataTables
     */
    public function getTaxesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        $query = Tax::with('translations');

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_key', 'name')
                          ->where('lang_value', 'like', "%{$search}%");
                })
                ->orWhere('tax_rate', 'like', "%{$search}%");
            });
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
                $langKey = $orderBy['key'] ?? 'name';
                $query->leftJoin('translations as t_sort', function($join) use ($langId, $langKey) {
                    $join->on('taxes.id', '=', 't_sort.translatable_id')
                         ->where('t_sort.translatable_type', '=', 'Modules\\CatalogManagement\\app\\Models\\Tax')
                         ->where('t_sort.lang_id', '=', $langId)
                         ->where('t_sort.lang_key', '=', $langKey);
                })
                ->orderBy('t_sort.lang_value', $orderDirection)
                ->select('taxes.*');
            } else {
                // Sorting by regular column
                $query->orderBy($orderBy, $orderDirection);
            }
        }

        return $query;
    }

    /**
     * Get taxes query for Select2 AJAX (with search support)
     */
    public function getAllTaxesQuery(array $filters = [])
    {
        $query = Tax::with('translations')->where('active', 1);

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%")
                          ->where('lang_key', 'name');
                })
                ->orWhere('tax_rate', 'like', "%{$search}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        return $query;
    }

    /**
     * Get tax by ID
     */
    public function getTaxById(int $id)
    {
        return Tax::with('translations')->findOrFail($id);
    }

    /**
     * Create a new tax
     */
    public function createTax(array $data)
    {
        return DB::transaction(function () use ($data) {
            $tax = Tax::create([
                'slug' => Str::uuid(),
                'tax_rate' => $data['tax_rate'],
                'active' => $data['active'] ?? 0,
            ]);

            // Set translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $tax->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'name',
                            'lang_value' => $translation['name'],
                        ]);
                    }
                }
            }
            
            $tax->refresh();
            $tax->load('translations');
            
            return $tax;
        });
    }

    /**
     * Update tax
     */
    public function updateTax(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $tax = Tax::findOrFail($id);

            $tax->update([
                'tax_rate' => $data['tax_rate'],
                'active' => $data['active'] ?? 0,
            ]);

            // Update translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $tax->translations()->updateOrCreate(
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
            
            $tax->refresh();
            $tax->load('translations');
            
            return $tax;
        });
    }

    /**
     * Delete tax
     */
    public function deleteTax(int $id)
    {
        $tax = Tax::findOrFail($id);
        $tax->translations()->delete();
        return $tax->delete();
    }

    /**
     * Get active taxes
     */
    public function getActiveTaxes()
    {
        return Tax::with('translations')->where('active', 1)->get();
    }
}
