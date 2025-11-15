<?php

namespace Modules\AreaSettings\app\Actions;

use Modules\AreaSettings\app\Models\Country;

class CountryQueryAction
{
    public function handle(array $filters = [])
    {
        $query = Country::query()->active()->with('translations')->filter($filters);
        return $query;
    }
}
