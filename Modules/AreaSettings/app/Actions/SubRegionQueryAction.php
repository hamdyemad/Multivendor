<?php

namespace Modules\AreaSettings\app\Actions;

use Modules\AreaSettings\app\Models\Region;
use Modules\AreaSettings\app\Models\SubRegion;

class SubRegionQueryAction
{
    public function handle(array $filters = [])
    {
        $query = SubRegion::query()->active()->with('translations')->filter($filters);
        return $query;
    }
}
