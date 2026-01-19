<?php

use Modules\Refund\app\Helpers\RefundHelper;
use Modules\CatalogManagement\app\Models\VendorProduct;

if (!function_exists('get_refund_days')) {
    /**
     * Get refund days for a vendor product
     * Returns product-specific refund days if set, otherwise returns system default
     * 
     * @param VendorProduct $vendorProduct
     * @return int Number of days allowed for refund
     */
    function get_refund_days(VendorProduct $vendorProduct): int
    {
        return RefundHelper::getRefundDays($vendorProduct);
    }
}

if (!function_exists('is_eligible_for_refund')) {
    /**
     * Check if a vendor product is eligible for refund based on delivery date
     * 
     * @param VendorProduct $vendorProduct
     * @param \Carbon\Carbon|string|null $deliveredAt The delivery date
     * @return bool
     */
    function is_eligible_for_refund(VendorProduct $vendorProduct, $deliveredAt = null): bool
    {
        return RefundHelper::isEligibleForRefund($vendorProduct, $deliveredAt);
    }
}

if (!function_exists('get_refund_deadline')) {
    /**
     * Get the refund deadline for a vendor product based on delivery date
     * 
     * @param VendorProduct $vendorProduct
     * @param \Carbon\Carbon|string $deliveredAt The delivery date
     * @return \Carbon\Carbon
     */
    function get_refund_deadline(VendorProduct $vendorProduct, $deliveredAt): \Carbon\Carbon
    {
        return RefundHelper::getRefundDeadline($vendorProduct, $deliveredAt);
    }
}

if (!function_exists('get_remaining_refund_days')) {
    /**
     * Get remaining days to request refund
     * 
     * @param VendorProduct $vendorProduct
     * @param \Carbon\Carbon|string $deliveredAt The delivery date
     * @return int Number of days remaining (0 if expired)
     */
    function get_remaining_refund_days(VendorProduct $vendorProduct, $deliveredAt): int
    {
        return RefundHelper::getRemainingRefundDays($vendorProduct, $deliveredAt);
    }
}
