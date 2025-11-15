<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Models\Activity;
use Modules\CategoryManagment\app\Models\Department;

class DepartmentQueryAction
{
    public function handle(array $filters = [])
    {
        $query = Department::query()->with('translations')->active()->withCount('activeCategories')->filter($filters);
        return $query;
    }
}
