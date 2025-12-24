<?php

namespace Modules\Vendor\app\Actions\Api;

use App\Models\Language;
use App\Models\Translation;
use Modules\Vendor\app\Models\Vendor;

class VendorQueryAction
{
    /**
     * Build the query with filters
     */
    public function handle(array $filters = [])
    {
        $query = Vendor::query()
            ->with('translations', 'country', 'logo', 'banner')
            ->withCount(['vendorProducts' => function($q) {
                $q->status('approved')->active();
            }])
            ->withCount('reviews')
            ->withAvg('reviews', 'star')
            ->active()
            ;

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
        $langId = Language::where('code', app()->getLocale())->value('id');

        switch ($sortBy) {
            case 'name':
                $query->whereHas('translations', function ($q) use ($langId) {
                    $q->where('translatable_type', Vendor::class)
                    ->where('lang_key', 'name')
                    ->where('lang_id', $langId);
                })
                ->orderBy(
                    Translation::select('lang_value')
                        ->whereColumn('translatable_id', 'vendors.id')
                        ->where('translatable_type', Vendor::class)
                        ->where('lang_key', 'name')
                        ->where('lang_id', $langId)
                        ->limit(1),
                    $sortType
                );
                break;
            case 'rating':
                $query->orderBy('reviews_avg_star', $sortType);
                break;
            default:
                $query->orderBy('created_at', $sortType);
        }

        return $query;
    }
}
