<?php

namespace Modules\Withdraw\app\Services;

use Illuminate\Support\Facades\Log;
use Modules\Withdraw\app\Interfaces\WithdrawRepositoryInterface;

class WithdrawService
{
    protected $withdrawRepositoryInterface;

    public function __construct(WithdrawRepositoryInterface $withdrawRepositoryInterface)
    {
        $this->withdrawRepositoryInterface = $withdrawRepositoryInterface;
    }

    /**
     * Get all departments with filters and pagination
     */
    public function getVendor()
    {
        try {
            return $this->withdrawRepositoryInterface->getVendor();
        } catch (\Exception $e) {
            Log::error('Error fetching departments: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getVendorBalance($vendor_id)
    {
        try {
            return $this->withdrawRepositoryInterface->getVendorBalance($vendor_id);
        } catch (\Exception $e) {
            Log::error('Error fetching departments: ' . $e->getMessage());
            throw $e;
        }
    }
}
