<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Models\Department;

class DepartmentQueryAction
{
    public function handle(array $filters = [])
    {
        $query = Department::query()
                    ->with('translations')
                    ->withCount('activeProducts')
                    ->withCount('activeCategories')
                    ->active()
                    ->where('view_status', 1)
                    ->filter($filters)
                    ->orderBy('sort_number', 'asc');
        return $query;
    }
}
