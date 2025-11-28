<?php

namespace Modules\Vendor\app\Interfaces\Api;

use Modules\Vendor\app\DTOs\VendorFilterDTO;

interface VendorApiRepositoryInterface
{
    /**
     * Get all vendors with filters and pagination
     */
    public function getAllVendors(VendorFilterDTO $filters);

    /**
     * Get vendor by ID
     */
    public function find(VendorFilterDTO $filters, $id);

    /**
     * Create a new vendor request
     */
    public function createVendorRequest(array $data);
}
