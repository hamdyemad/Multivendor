<?php

namespace Modules\Vendor\app\Interfaces\Api;

interface VendorApiRepositoryInterface
{
    /**
     * Get all vendors with filters and pagination
     */
    public function getAllVendors(array $filters = []);

    /**
     * Get vendor by ID
     */
    public function find(array $filters = [], $id);
}
