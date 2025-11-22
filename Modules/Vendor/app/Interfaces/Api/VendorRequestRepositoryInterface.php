<?php

namespace Modules\Vendor\app\Interfaces\Api;

interface VendorRequestRepositoryInterface
{
    /**
     * Create a new vendor request
     */
    public function createVendorRequest(array $data);

    /**
     * Get all vendor requests with filters and pagination
     */
    public function getAllVendorRequests(array $filters = [], int $perPage = 10);

    /**
     * Get vendor request by ID
     */
    public function getVendorRequestById(int $id);

    /**
     * Update vendor request
     */
    public function updateVendorRequest(int $id, array $data);

    /**
     * Delete vendor request (soft delete)
     */
    public function deleteVendorRequest(int $id);

    /**
     * Approve vendor request
     */
    public function approveVendorRequest(int $id);

    /**
     * Reject vendor request
     */
    public function rejectVendorRequest(int $id, string $reason = null);
}
