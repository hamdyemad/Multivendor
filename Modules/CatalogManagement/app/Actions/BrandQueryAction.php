<?php

namespace Modules\CatalogManagement\app\Actions;

use App\Models\Language;
use App\Models\Translation;
use Modules\CatalogManagement\app\Models\Brand;

class BrandQueryAction
{
    /**
     * Build the query with filters
     */
    public function handle(array $filters = [])
    {
        $query = Brand::query()->active();

        // Load relationships
        $query->with(['translations', 'logo', 'cover']);

        if (!empty($filters)) {
            $query->filter($filters);
        }

        // Apply products count with combined filters
        $query->withCount(['products' => function ($q) use ($filters) {
            // Vendor filter for product count
            if (isset($filters['vendor_id'])) {
                $vendorIdentifier = $filters['vendor_id'];
                $vendor = \Modules\Vendor\app\Models\Vendor::where('slug', $vendorIdentifier)
                    ->orWhere('id', $vendorIdentifier)->first();
                if ($vendor) {
                    $q->whereHas('vendorProducts', function ($vq) use ($vendor) {
                        $vq->where('vendor_id', $vendor->id)
                            ->where('is_active', true)
                            ->where('status', 'approved');
                    });
                }
            }

            // Department filter for product count
            if (isset($filters['department_id'])) {
                $deptId = $filters['department_id'];
                $q->where(function ($dq) use ($deptId) {
                    $dq->where('department_id', $deptId)
                        ->orWhereHas('department', function ($sdq) use ($deptId) {
                            $sdq->where('id', $deptId)
                                ->orWhere('slug', $deptId);
                        });
                });
            }
        }]);

        $query = $this->applySorting($query, $filters);

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
                    $q->where('translatable_type', Brand::class)
                    ->where('lang_key', 'name')
                    ->where('lang_id', $langId);
                })
                ->orderBy(
                    Translation::select('lang_value')
                        ->whereColumn('translatable_id', 'brands.id')
                        ->where('translatable_type', Brand::class)
                        ->where('lang_key', 'name')
                        ->where('lang_id', $langId)
                        ->limit(1),
                    $sortType
                )
                ;
            default:
                $query->orderBy('created_at', $sortType);
        }

        return $query;
    }
}
