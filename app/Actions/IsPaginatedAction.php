<?php

namespace App\Actions;

class IsPaginatedAction
{
    public function handle($query, $per_page, $paginated = null)
    {
        $per_page = $per_page ?? 10;
        
        // If paginated is null (not passed), return all results without pagination
        if ($paginated === null) {
            return $query->get();
        }
        
        // Convert string values to boolean
        if (is_string($paginated)) {
            $paginated = in_array(strtolower($paginated), ['true', '1', 'yes', 'ok'], true);
        }
        
        if ($paginated) {
            return $query->paginate($per_page);
        }
        
        // When paginated is explicitly false, return all results
        return $query->get();
    }
}
