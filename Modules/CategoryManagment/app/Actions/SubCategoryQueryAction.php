<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Models\SubCategory;

class SubCategoryQueryAction
{
    public function handle(array $filters = [])
    {
        $query = SubCategory::query()
                    ->active()
                    ->where('view_status', 1)
                    ->withCount('activeProducts')
                    ->with(['category','category.department', 'translations'])
                    ->filter($filters);

        // Handle sorting
        $sort = $filters['sort'] ?? null;
        $sortType = strtolower($filters['sort_type'] ?? 'desc');
        
        if ($sort === 'products') {
            $query->orderBy('active_products_count', $sortType);
        } elseif ($sort === 'created_at') {
            $query->orderBy('created_at', $sortType);
        } else {
            // Default sorting by sort_number
            $query->orderBy('sort_number', 'asc');
        }

        return $query;
    }
}
