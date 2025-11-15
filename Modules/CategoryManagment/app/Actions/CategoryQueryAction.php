<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Models\Category;

class CategoryQueryAction
{
    public function handle(array $filters = [])
    {
        $query = Category::query()->with('translations')->active()->withCount('activeSubs')->filter($filters);
        return $query;
    }
}
