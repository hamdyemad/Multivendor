<?php

namespace Modules\Refund\app\Observers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Refund\app\Models\RefundRequest;
use Modules\Refund\app\Services\RefundNotificationService;
use Modules\CatalogManagement\app\Services\StockBookingService;
use Modules\SystemSetting\app\Services\UserPointsService;

class RefundRequestObserver
{
    public function __construct(
        protected RefundNotificationService $notificationService,
        protected StockBookingService $stockBookingService,
        protected UserPointsService $userPointsService
    ) {}

    /**
     * Handle the RefundRequest "created" event.
     */
    public function created(RefundRequest $refundRequest): void
    {
        // Only send notifications for vendor refunds (not parent)
        if (!$refundRequest->is_parent && $refundRequest->vendor_id) {
            // Notify vendor about new refund request
            $this->notificationService->notifyVendorNewRefund($refundRequest);
        }
        
        // If it's a parent refund, notify customer
        if ($refundRequest->is_parent) {
            $this->notificationService->notifyRefundCreated($refundRequest);
        }
    }

    /**
     * Handle the RefundRequest "updated" event.
     */
    public function updated(RefundRequest $refundRequest): void
    {
        // Handle status change notifications
        if ($refundRequest->wasChanged('status')) {
            $oldStatus = $refundRequest->getOriginal('status');
            $newStatus = $refundRequest->status;
            
            // Notify customer about status change
            $this->notificationService->notifyCustomerStatusChange(
                $refundRequest,
                $oldStatus,
                $newStatus
            );
            
            // Notify vendor about status change
            $this->notificationService->notifyVendorStatusChange(
                $refundRequest,
                $oldStatus,
                $newStatus
            );
            
            // Handle refund completion
            if ($newStatus === 'refunded') {
                $this->handleRefundCompletion($refundRequest);
            }
        }
    }

    /**
     * Handle refund completion
     */
    protected function handleRefundCompletion(RefundRequest $refundRequest): void
    {
        DB::transaction(function () use ($refundRequest) {
            $vendor = $refundRequest->vendor;
            $order = $refundRequest->order;
            $customer = $refundRequest->customer;
            
            // 1. Update Customer Points using service
            if ($refundRequest->points_to_deduct > 0) {
                $this->userPointsService->deductPoints(
                    userId: $customer->id,
                    points: $refundRequest->points_to_deduct,
                    transactionableType: RefundRequest::class,
                    transactionableId: $refundRequest->id,
                    description: "Points deducted for refund: {$refundRequest->refund_number}"
                );
            }
            
            if ($refundRequest->points_used > 0) {
                $this->userPointsService->addPoints(
                    userId: $customer->id,
                    points: $refundRequest->points_used,
                    transactionableType: RefundRequest::class,
                    transactionableId: $refundRequest->id,
                    description: "Points refunded for refund: {$refundRequest->refund_number}"
                );
            }
            
            // 2. Mark Order Products as Refunded
            foreach ($refundRequest->items as $item) {
                $orderProduct = $item->orderProduct;
                $orderProduct->is_refunded = true;
                $orderProduct->refunded_amount = $item->refund_amount;
                $orderProduct->refunded_at = now();
                $orderProduct->save();
            }
            
            // 3. Update Order - Track Total Refunded Amount
            $order->refunded_amount = ($order->refunded_amount ?? 0) + $refundRequest->total_refund_amount;
            $order->save();
            
            // 4. Reverse Stock Bookings using service
            $orderProductIds = $refundRequest->items->pluck('order_product_id')->toArray();
            $this->stockBookingService->releaseRefundedStock(
                orderId: $order->id,
                orderProductIds: $orderProductIds,
                refundNumber: $refundRequest->refund_number
            );
            
            // 5. Log the refund completion
            $commissionReversed = $this->calculateCommissionReversal($refundRequest);
            
            Log::info('Refund completed', [
                'refund_number' => $refundRequest->refund_number,
                'order_id' => $order->id,
                'vendor_id' => $vendor->id,
                'total_refund' => $refundRequest->total_refund_amount,
                'commission_reversed' => $commissionReversed,
            ]);
        });
    }

    /**
     * Calculate commission reversal
     */
    protected function calculateCommissionReversal(RefundRequest $refundRequest): float
    {
        $totalCommission = 0;
        
        foreach ($refundRequest->items as $item) {
            $orderProduct = $item->orderProduct;
            
            // Get commission percentage (product or department)
            $commissionPercent = $orderProduct->commission > 0 
                ? $orderProduct->commission 
                : ($orderProduct->vendorProduct->product->department->commission ?? 0);
            
            // Calculate commission on refunded amount (price + shipping)
            $refundableAmount = $item->total_price + $item->shipping_amount;
            $commission = ($refundableAmount * $commissionPercent) / 100;
            
            $totalCommission += $commission;
        }
        
        return $totalCommission;
    }
}
