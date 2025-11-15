<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Models\Activity;

class ActivityQueryAction
{
    public function handle(array $filters = [])
    {
        $query = Activity::query()->with('translations')->active()->withCount('activeDepartments')->filter($filters);
        return $query;
    }
}
