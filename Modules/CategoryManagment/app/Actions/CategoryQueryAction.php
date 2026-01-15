<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Models\Category;

class CategoryQueryAction
{
    public function handle(array $filters = [])
    {
        $sortBy = $filters['sort'] ?? 'sort_number';
        $sortType = $filters['sort_type'] ?? 'asc';
        
        // Determine subcategory sort
        $subCategorySort = 'sort_number';
        $subCategorySortType = 'asc';
        
        if ($sortBy === 'sub_categories_products') {
            $subCategorySort = 'active_products_count';
            $subCategorySortType = $sortType;
        }
        
        $query = Category::query()
                    ->active()
                    ->where('view_status', 1)
                    ->withCount('activeSubs')
                    ->withCount(['activeProducts as active_products_count'])
                    ->with(['department' => function($q) {
                        $q->withCount(['activeProducts as active_products_count']);
                    }, 'translations', 
                        // 'activeSubs' => function($q) use ($subCategorySort, $subCategorySortType) {
                        //     $q->withCount(['activeProducts as active_products_count'])
                        //     ->orderBy($subCategorySort, $subCategorySortType);
                        // }
                    ])
                    ->filter($filters)
                    ->orderBy('sort_number', $subCategorySortType);
        return $query;
    }
}
