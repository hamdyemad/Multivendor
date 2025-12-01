<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Models\SubCategory;

class SubCategoryQueryAction
{
    public function handle(array $filters = [])
    {
        $query = SubCategory::query()
                    ->active()
                    ->withCount('activeProducts')
                    ->with(['category','category.department', 'translations'])
                    ->filter($filters);
        return $query;
    }
}
