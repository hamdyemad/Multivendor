<?php

namespace Modules\Refund\app\Services;

use Modules\Refund\app\Models\RefundSetting;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderProduct;
use Modules\Order\app\Models\OrderExtraFeeDiscount;

class RefundCalculationService
{
    protected RefundSetting $settings;

    public function __construct()
    {
        $this->settings = RefundSetting::getInstance();
    }

    /**
     * Calculate refund for given order products
     */
    public function calculateRefund(Order $order, array $orderProductIds, int $vendorId): array
    {
        $orderProducts = OrderProduct::whereIn('id', $orderProductIds)
            ->where('vendor_id', $vendorId)
            ->get();

        if ($orderProducts->isEmpty()) {
            throw new \Exception('No valid products found for refund');
        }

        // Step 1: Calculate product amounts
        $items = [];
        $totalProductsAmount = 0;
        $totalShippingAmount = 0;
        $totalTaxAmount = 0;
        $totalDiscountAmount = 0;

        foreach ($orderProducts as $orderProduct) {
            $itemData = $this->calculateItemRefund($orderProduct);
            $items[] = $itemData;

            $totalProductsAmount += $itemData['total_price'];
            $totalShippingAmount += $itemData['shipping_amount'];
            $totalTaxAmount += $itemData['tax_amount'];
            $totalDiscountAmount += $itemData['discount_amount'];
        }

        // Step 2: Calculate vendor fees and discounts
        $vendorFeesDiscounts = $this->calculateVendorFeesDiscounts($order, $vendorId, $totalProductsAmount);

        // Step 3: Calculate promo code amount
        $promoCodeAmount = $this->calculatePromoCodeAmount($order, $vendorId, $totalProductsAmount);

        // Step 4: Calculate return shipping cost
        $returnShippingCost = $this->calculateReturnShippingCost($order, $orderProducts);

        // Step 5: Calculate points
        $pointsData = $this->calculatePoints($order, $vendorId, $totalProductsAmount);

        // Step 6: Calculate final refund amount
        $totalRefundAmount = $totalProductsAmount 
            + $totalTaxAmount 
            + ($this->settings->refund_original_shipping ? $totalShippingAmount : 0)
            + $vendorFeesDiscounts['discounts']
            + $promoCodeAmount
            - $vendorFeesDiscounts['fees']
            - $returnShippingCost
            - $pointsData['points_value_used'];

        return [
            'items' => $items,
            'total_products_amount' => $totalProductsAmount,
            'total_shipping_amount' => $totalShippingAmount,
            'total_tax_amount' => $totalTaxAmount,
            'total_discount_amount' => $totalDiscountAmount,
            'vendor_fees_amount' => $vendorFeesDiscounts['fees'],
            'vendor_discounts_amount' => $vendorFeesDiscounts['discounts'],
            'promo_code_amount' => $promoCodeAmount,
            'return_shipping_cost' => $returnShippingCost,
            'points_used' => $pointsData['points_used'],
            'points_to_deduct' => $pointsData['points_to_deduct'],
            'total_refund_amount' => $totalRefundAmount,
        ];
    }

    /**
     * Calculate refund for single item
     */
    protected function calculateItemRefund(OrderProduct $orderProduct): array
    {
        $unitPrice = $orderProduct->price / $orderProduct->quantity;
        $totalPrice = $orderProduct->price;
        $taxAmount = $orderProduct->tax ?? 0;
        $discountAmount = $orderProduct->discount ?? 0;
        $shippingAmount = $orderProduct->shipping_cost ?? 0;

        $refundAmount = $totalPrice + $taxAmount + $shippingAmount - $discountAmount;

        return [
            'order_product_id' => $orderProduct->id,
            'product_variant_id' => $orderProduct->vendor_product_variant_id,
            'quantity' => $orderProduct->quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'shipping_amount' => $shippingAmount,
            'refund_amount' => $refundAmount,
        ];
    }

    /**
     * Calculate vendor fees and discounts
     */
    protected function calculateVendorFeesDiscounts(Order $order, int $vendorId, float $refundedAmount): array
    {
        $vendorFees = OrderExtraFeeDiscount::where('order_id', $order->id)
            ->where('vendor_id', $vendorId)
            ->where('type', 'fee')
            ->sum('cost');

        $vendorDiscounts = OrderExtraFeeDiscount::where('order_id', $order->id)
            ->where('vendor_id', $vendorId)
            ->where('type', 'discount')
            ->sum('cost');

        // Calculate vendor's total in order
        $vendorTotal = OrderProduct::where('order_id', $order->id)
            ->where('vendor_id', $vendorId)
            ->sum('price');

        if ($vendorTotal == 0) {
            return ['fees' => 0, 'discounts' => 0];
        }

        // Calculate proportional fees/discounts
        $refundPercentage = $refundedAmount / $vendorTotal;

        return [
            'fees' => $vendorFees * $refundPercentage,
            'discounts' => $vendorDiscounts * $refundPercentage,
        ];
    }

    /**
     * Calculate promo code amount
     */
    protected function calculatePromoCodeAmount(Order $order, int $vendorId, float $refundedAmount): float
    {
        if (!$order->customer_promo_code_amount || $order->customer_promo_code_amount == 0) {
            return 0;
        }

        // Calculate vendor's share of total order
        $vendorTotal = OrderProduct::where('order_id', $order->id)
            ->where('vendor_id', $vendorId)
            ->sum('price');

        $orderTotal = $order->total_product_price;

        if ($orderTotal == 0) {
            return 0;
        }

        $vendorPercentage = $vendorTotal / $orderTotal;
        $vendorPromoDiscount = $order->customer_promo_code_amount * $vendorPercentage;

        // Calculate proportional promo for refunded items
        $refundPercentage = $refundedAmount / $vendorTotal;

        return $vendorPromoDiscount * $refundPercentage;
    }

    /**
     * Calculate return shipping cost
     */
    protected function calculateReturnShippingCost(Order $order, $orderProducts): float
    {
        if (!$this->settings->customer_pays_return_shipping) {
            return 0;
        }

        // TODO: Integrate with existing shipping system
        // For now, use the sum of original shipping costs
        return $orderProducts->sum('shipping_cost');
    }

    /**
     * Calculate points
     */
    protected function calculatePoints(Order $order, int $vendorId, float $refundedAmount): array
    {
        $pointsUsed = 0;
        $pointsToDeduct = 0;

        // Calculate points used for this vendor
        if ($order->points_used > 0) {
            $vendorTotal = OrderProduct::where('order_id', $order->id)
                ->where('vendor_id', $vendorId)
                ->sum('price');

            $orderTotal = $order->total_product_price;

            if ($orderTotal > 0) {
                $vendorPointsUsed = $order->points_used * ($vendorTotal / $orderTotal);
                $refundPercentage = $refundedAmount / $vendorTotal;
                $pointsUsed = $vendorPointsUsed * $refundPercentage;
            }
        }

        // Calculate points earned from vendor (to be deducted)
        // Assuming 1 point per 1 EGP
        $pointsToDeduct = (int) $refundedAmount;

        return [
            'points_used' => $pointsUsed,
            'points_value_used' => $order->points_cost > 0 
                ? ($pointsUsed / $order->points_used) * $order->points_cost 
                : 0,
            'points_to_deduct' => $pointsToDeduct,
        ];
    }
}
