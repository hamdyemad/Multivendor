<?php

namespace Modules\CatalogManagement\app\Actions;

use Modules\CatalogManagement\app\Models\VendorProduct;

class ProductQueryAction
{
    /**
     * Handle the product query with filters and sorting
     */
    public function handle(array $filters = [])
    {
        $query = VendorProduct::query()
            ->active()
            ->status(VendorProduct::STATUS_APPROVED)
            ->with([
                'product' => function ($q) {
                    $q->with(['brand', 'variants', 'attachments', 'translations']);
                },
                'vendor',
                'tax',
            ]);

        if (!empty($filters)) {
            $query->filter($filters);
        }

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

        switch ($sortBy) {
            case 'name':
                $query->join('translations', function ($join) {
                    $join->on('products.id', '=', 'translations.translatable_id')
                         ->where('translations.translatable_type', 'Modules\\CatalogManagement\\app\\Models\\Product')
                         ->where('translations.lang_key', 'name');
                })
                ->orderBy('translations.lang_value', $sortType)
                ->select('products.*')->distinct('products.id');
                break;
            case 'price':
                $query->with(['variants' => function ($q) {
                    $q->where('has_discount', true)->whereNotNull('discount_end_date')
                      ->where('discount_end_date', '>', now());
                }])
                ->orderByRaw("(
                    SELECT COALESCE(MIN(price), 0)
                    FROM vendor_product_variants
                    WHERE vendor_product_id = vendor_products.id
                    AND discount_end_date IS NOT NULL
                    AND discount_end_date > NOW() AND has_discount = true
                ) {$sortType}")
                ->orderByRaw("(
                    SELECT COALESCE(MIN(price), 0)
                    FROM vendor_product_variants
                    WHERE vendor_product_id = vendor_products.id
                    AND has_discount = true
                ) {$sortType}");
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')
                    ->orderBy('reviews_avg_rating', $sortType);
                break;
            case 'views':
                $query->orderBy('views', $sortType);
                break;
            case 'sales':
                $query->orderBy('sales', $sortType);
                break;
            default:
                $query->orderBy('created_at', $sortType);
        }

        return $query;
    }
}
