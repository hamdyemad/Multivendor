<?php

namespace Modules\Vendor\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\Vendor\app\Actions\Api\VendorQueryAction;
use Modules\Vendor\app\Interfaces\Api\VendorApiRepositoryInterface;

class VendorApiRepository implements VendorApiRepositoryInterface
{
    public function __construct(protected VendorQueryAction $query, protected IsPaginatedAction $paginated) {}

    /**
     * Get all Vendors with filters and pagination
     */
    public function getAllVendors(array $filters = [])
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $paginated, $filters["per_page"] ?? null);
        return $result;
    }

    /**
     * Get Vendor by ID
     */
    public function find(array $filters = [], $id)
    {
        return $this->query->handle($filters)->with('activeActivities')->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))->firstOrFail();
    }
}
