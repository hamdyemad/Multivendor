<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Modules\Order\app\Interfaces\OrderRepositoryInterface;

class SyncExtras
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    /**
     * Handle the pipeline.
     *
     * Syncs order extras (fees and discounts) to the database using repository.
     * This step creates OrderExtraFeeDiscount records for all fees and discounts.
     * 
     * If created by vendor: fees/discounts are assigned to that vendor only
     * If created by admin: fees/discounts are distributed proportionally among all vendors
     */
    public function handle($payload, Closure $next)
    {

        $data = $payload['data'];
        $context = $payload['context'];

        $order = $context['order'];
        $fees = $context['fees'];
        $discounts = $context['discounts'];
        $createdByVendorId = $context['created_by_vendor_id'] ?? null;

        if ($createdByVendorId) {
            // Vendor created: assign to specific vendor
            $this->orderRepository->syncOrderExtras($order, $fees, 'fee', $createdByVendorId);
            $this->orderRepository->syncOrderExtras($order, $discounts, 'discount', $createdByVendorId);
        } else {
            // Admin created: distribute proportionally among all vendors
            $this->distributeExtrasAmongVendors($order, $fees, 'fee');
            $this->distributeExtrasAmongVendors($order, $discounts, 'discount');
        }

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }

    /**
     * Distribute extras (fees/discounts) proportionally among all vendors in the order
     */
    private function distributeExtrasAmongVendors($order, $extras, $type)
    {
        if (empty($extras)) {
            return;
        }

        // Get all vendors in this order
        $vendorProducts = $order->products->groupBy(function($product) {
            return $product->vendorProduct?->vendor_id;
        });

        // Calculate total order value
        $orderProductTotal = $order->products->sum('price');

        if ($orderProductTotal <= 0) {
            return;
        }

        // Distribute each extra among vendors
        foreach ($extras as $extra) {
            $totalAmount = $extra['amount'];
            $reason = $extra['reason'];

            foreach ($vendorProducts as $vendorId => $products) {
                if (!$vendorId) {
                    continue;
                }

                // Calculate vendor's share of total order
                $vendorProductTotal = $products->sum('price');
                $vendorShareRatio = $vendorProductTotal / $orderProductTotal;

                // Calculate vendor's share of this extra
                $vendorShare = $totalAmount * $vendorShareRatio;

                // Store vendor's share
                \Modules\Order\app\Models\OrderExtraFeeDiscount::create([
                    'order_id' => $order->id,
                    'vendor_id' => $vendorId,
                    'cost' => round($vendorShare, 2),
                    'reason' => $reason . ' (Shared)',
                    'type' => $type,
                ]);
            }
        }
    }
}
