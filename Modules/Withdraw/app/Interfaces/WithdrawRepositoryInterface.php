<?php

namespace Modules\Withdraw\app\Interfaces;

interface WithdrawRepositoryInterface
{
    /**
     * Get all departments with filters and pagination
     */
    public function getVendor();
    public function getVendorBalance($vendor_id);
}
