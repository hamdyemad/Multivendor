<?php

namespace Modules\Vendor\app\Services\Api;

use Modules\Vendor\app\Interfaces\Api\VendorApiRepositoryInterface;

class VendorApiService
{
    protected $VendorRepository;

    public function __construct(VendorApiRepositoryInterface $VendorRepository)
    {
        $this->VendorRepository = $VendorRepository;
    }

    /**
     * Get all Vendors with filters and pagination
     */
    public function getAllVendors(array $filters = [])
    {
        return $this->VendorRepository->getAllVendors($filters);
    }

    /**
     * Get Vendor by ID
     */
    public function find(array $filters = [], $id)
    {
        return $this->VendorRepository->find($filters, $id);
    }
}
