<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Models\Category;

class CategoryQueryAction
{
    public function handle(array $filters = [])
    {
        $query = Category::query()
                    ->active()
                    ->withCount('activeSubs')
                    ->withCount('activeProducts')
                    ->with(['department', 'translations', 'activeSubs'])
                    ->filter($filters);
        return $query;
    }
}
