<?php

use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\Refund\app\Helpers\RefundHelper;

if (!function_exists('get_refund_days')) {
    /**
     * Get refund days for a vendor product.
     * Returns product-specific refund days if set, otherwise returns system default.
     *
     * @param VendorProduct|null $vendorProduct
     * @return int
     */
    function get_refund_days(?VendorProduct $vendorProduct): int
    {
        return RefundHelper::getRefundDays($vendorProduct);
    }
}

if (!function_exists('is_eligible_for_refund')) {
    /**
     * Check if a vendor product is eligible for refund based on delivery date.
     *
     * @param VendorProduct|null $vendorProduct
     * @param \Carbon\Carbon|string|null $deliveredAt
     * @return bool
     */
    function is_eligible_for_refund(?VendorProduct $vendorProduct, $deliveredAt): bool
    {
        return RefundHelper::isEligibleForRefund($vendorProduct, $deliveredAt);
    }
}

if (!function_exists('get_refund_deadline')) {
    /**
     * Get the refund deadline date for a vendor product.
     *
     * @param VendorProduct|null $vendorProduct
     * @param \Carbon\Carbon|string|null $deliveredAt
     * @return \Carbon\Carbon|null
     */
    function get_refund_deadline(?VendorProduct $vendorProduct, $deliveredAt): ?\Carbon\Carbon
    {
        return RefundHelper::getRefundDeadline($vendorProduct, $deliveredAt);
    }
}
