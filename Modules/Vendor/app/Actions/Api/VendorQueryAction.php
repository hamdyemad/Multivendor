<?php

namespace Modules\Vendor\app\Actions\Api;

use Modules\Vendor\app\Models\Vendor;

class VendorQueryAction
{
    /**
     * Build the query with filters
     */
    public function handle(array $filters = [])
    {
        $query = Vendor::query()->with('translations')->active();

        // Load relationships
        $query->with(['translations', 'country', 'logo', 'banner']);

        if (!empty($filters)) {
            $query->filter($filters);
        }

        $query = $this->applySorting($query, $filters);

        // Order by latest first
        $query->latest();

        return $query;
    }


    protected function applySorting($query, array $filters)
    {
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortType = $filters['sort_type'] ?? 'desc';

        if (!in_array($sortType, ['asc', 'desc'])) {
            $sortType = 'desc';
        }

        switch ($sortBy) {
            case 'name':
                $query->join('translations', function($join) {
                    $join->on('vendors.id', '=', 'translations.translatable_id')
                         ->where('translations.translatable_type', 'Modules\\Vendor\\app\\Models\\Vendor')
                         ->where('translations.lang_key', 'name');
                })
                ->orderBy('translations.lang_value', $sortType)
                ->select('vendors.*')->distinct('vendors.id');
                break;
            case 'rating':
                // $query->withAvg('reviews', 'rating')
                //     ->orderBy('reviews_avg_rating', $sortType);
                break;
            default:
                $query->orderBy('created_at', $sortType);
        }

        return $query;
    }
}
