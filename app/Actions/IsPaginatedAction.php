<?php

namespace App\Actions;

class IsPaginatedAction
{
    public function handle($query, $per_page, $paginated = false)
    {
        $per_page = $per_page ?? 15;
        
        // Convert string values to boolean
        if (is_string($paginated)) {
            $paginated = in_array(strtolower($paginated), ['true', '1', 'yes', 'ok'], true);
        }
        
        if ($paginated) {
            return $query->paginate($per_page);
        }
        
        // When not paginated, still limit results to prevent loading too many records
        // Use per_page as the limit, or default to 50 max
        $limit = min($per_page, 50);
        return $query->limit($limit)->get();
    }
}
