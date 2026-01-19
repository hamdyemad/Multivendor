<?php

namespace Modules\Refund\app\Helpers;

use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\Refund\app\Models\RefundSetting;

class RefundHelper
{
    /**
     * Get refund days for a vendor product
     * Returns product-specific refund days if set, otherwise returns system default
     * 
     * @param VendorProduct $vendorProduct
     * @return int Number of days allowed for refund
     */
    public static function getRefundDays(VendorProduct $vendorProduct): int
    {
        // If vendor product has specific refund days set, use it
        if ($vendorProduct->refund_days !== null && $vendorProduct->refund_days > 0) {
            return $vendorProduct->refund_days;
        }

        // Otherwise, get default from system settings
        $settings = RefundSetting::getInstance();
        return $settings->refund_processing_days ?? 7;
    }

    /**
     * Check if a vendor product is eligible for refund based on delivery date
     * 
     * @param VendorProduct $vendorProduct
     * @param \Carbon\Carbon|string|null $deliveredAt The delivery date
     * @return bool
     */
    public static function isEligibleForRefund(VendorProduct $vendorProduct, $deliveredAt = null): bool
    {
        // Check if refunds are enabled for this product
        if (!$vendorProduct->is_able_to_refund) {
            return false;
        }

        // If no delivery date provided, assume it's eligible (will be checked later)
        if (!$deliveredAt) {
            return true;
        }

        // Convert to Carbon instance if string
        $deliveredAt = $deliveredAt instanceof \Carbon\Carbon 
            ? $deliveredAt 
            : \Carbon\Carbon::parse($deliveredAt);

        // Get refund days (product-specific or system default)
        $refundDays = self::getRefundDays($vendorProduct);

        // Check if within refund window
        $refundDeadline = $deliveredAt->copy()->addDays($refundDays);
        
        return now()->lte($refundDeadline);
    }

    /**
     * Get the refund deadline for a vendor product based on delivery date
     * 
     * @param VendorProduct $vendorProduct
     * @param \Carbon\Carbon|string $deliveredAt The delivery date
     * @return \Carbon\Carbon
     */
    public static function getRefundDeadline(VendorProduct $vendorProduct, $deliveredAt): \Carbon\Carbon
    {
        $deliveredAt = $deliveredAt instanceof \Carbon\Carbon 
            ? $deliveredAt 
            : \Carbon\Carbon::parse($deliveredAt);

        $refundDays = self::getRefundDays($vendorProduct);

        return $deliveredAt->copy()->addDays($refundDays);
    }

    /**
     * Get remaining days to request refund
     * 
     * @param VendorProduct $vendorProduct
     * @param \Carbon\Carbon|string $deliveredAt The delivery date
     * @return int Number of days remaining (0 if expired)
     */
    public static function getRemainingRefundDays(VendorProduct $vendorProduct, $deliveredAt): int
    {
        $deadline = self::getRefundDeadline($vendorProduct, $deliveredAt);
        $remainingDays = now()->diffInDays($deadline, false);
        
        return max(0, (int) $remainingDays);
    }
}
