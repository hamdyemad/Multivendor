<?php

namespace App\Actions;

class IsPaginatedAction
{
    public function handle($query, $per_page, $paginated = false)
    {
        $per_page = $per_page ?? 10;
        $result = $paginated ? $query->paginate($per_page) : $query->get();

        return $result;
    }
}
