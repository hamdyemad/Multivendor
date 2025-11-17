<?php

namespace Modules\Vendor\app\Actions\Api;

use Modules\Vendor\app\Models\Vendor;

class VendorQueryAction
{
    /**
     * Build the query with filters
     */
    public function handle(array $filters = [])
    {
        $query = Vendor::query()->with('translations')->active()->filter($filters);

        // Load relationships
        $query->with(['translations', 'country', 'logo', 'banner']);

        // Order by latest first
        $query->latest();

        return $query;
    }
}
