<?php

namespace Modules\Vendor\app\Interfaces;

interface VendorRequestRepositoryInterface
{
    public function createVendorRequest(array $data);
    public function getAllVendorRequests(array $filters = [], int $perPage = 10);
    public function getVendorRequestById(int $id);
    public function updateVendorRequest(int $id, array $data);
    public function deleteVendorRequest(int $id);
    public function approveVendorRequest(int $id);
    public function rejectVendorRequest(int $id, string $reason = null);
}
