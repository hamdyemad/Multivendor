<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Models\SubCategory;

class SubCategoryQueryAction
{
    public function handle(array $filters = [])
    {
        $query = SubCategory::query()->with('translations')->active()->filter($filters);
        return $query;
    }
}
