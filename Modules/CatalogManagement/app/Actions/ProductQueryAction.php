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
            ->with(['product', 'brand', 'department', 'category', 'subCategory', 'variants', 'attachments']);

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
                $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(translations, '$.title')) {$sortType}");
                break;
            case 'price':
                $query->orderByRaw("(SELECT MIN(price) FROM variants_configurations WHERE product_id = products.id) {$sortType}");
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
