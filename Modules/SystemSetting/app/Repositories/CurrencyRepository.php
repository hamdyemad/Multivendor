<?php

namespace Modules\SystemSetting\app\Repositories;

use Modules\SystemSetting\app\Interfaces\CurrencyRepositoryInterface;
use Modules\SystemSetting\app\Models\Currency;
use Illuminate\Support\Facades\DB;

class CurrencyRepository implements CurrencyRepositoryInterface
{
    /**
     * Get all currencies with filters and pagination
     */
    public function getAllCurrencies(array $filters = [], ?int $perPage = 15)
    {
        $query = Currency::with('translations');

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                })->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%");
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

        // Return paginated or all records
        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get currencies query for DataTables
     */
    public function getCurrenciesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        $query = Currency::with('translations');

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                })
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('symbol', 'like', "%{$search}%");
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
                $query->leftJoin('translations as t_sort', function($join) use ($langId) {
                    $join->on('currencies.id', '=', 't_sort.translatable_id')
                         ->where('t_sort.translatable_type', '=', Currency::class)
                         ->where('t_sort.lang_id', '=', $langId)
                         ->where('t_sort.lang_key', '=', 'name');
                })
                ->orderBy('t_sort.lang_value', $orderDirection)
                ->select('currencies.*');
            } else {
                // Sorting by regular column
                $query->orderBy($orderBy, $orderDirection);
            }
        }

        return $query;
    }

    /**
     * Get currency by ID
     */
    public function getCurrencyById(int $id)
    {
        return Currency::with('translations')->findOrFail($id);
    }

    /**
     * Create a new currency
     */
    public function createCurrency(array $data)
    {
        return DB::transaction(function () use ($data) {
            $currency = Currency::create([
                'code' => $data['code'],
                'symbol' => $data['symbol'],
                'active' => $data['active'] ?? 0,
            ]);

            // Set translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $currency->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'name',
                            'lang_value' => $translation['name'],
                        ]);
                    }
                }
            }
            
            return $currency;
        });
    }

    /**
     * Update currency
     */
    public function updateCurrency(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $currency = Currency::findOrFail($id);

            $currency->update([
                'code' => $data['code'],
                'symbol' => $data['symbol'],
                'active' => $data['active'] ?? 0,
            ]);

            // Update translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $currency->translations()->updateOrCreate(
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

            $currency->refresh();
            $currency->load('translations');

            return $currency;
        });
    }

    /**
     * Delete currency
     */
    public function deleteCurrency(int $id)
    {
        $currency = Currency::findOrFail($id);
        $currency->translations()->delete();
        return $currency->delete();
    }

    /**
     * Get active currencies
     */
    public function getActiveCurrencies()
    {
        return Currency::with('translations')->where('active', 1)->get();
    }
}
