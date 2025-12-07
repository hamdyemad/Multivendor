<?php

namespace App\Actions;

class IsPaginatedAction
{
    public function handle($query, $paginated = false, $per_page)
    {
        $per_page = $per_page ?? 10;
        $result = $paginated ? $query->paginate($per_page) : $query->get();

        return $result;
    }
}
