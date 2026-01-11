<?php

namespace Modules\Order\app\Observers;

use App\Helpers\PointsHelper;
use Illuminate\Support\Facades\Log;
use Modules\CatalogManagement\app\Models\StockBooking;
use Modules\Order\app\Models\OrderProduct;
use Modules\Order\app\Models\OrderStage;
use Modules\Order\app\Models\VendorOrderStage;
use Modules\SystemSetting\app\Models\PointsSystem;
use Modules\SystemSetting\app\Models\UserPoints;
use Modules\SystemSetting\app\Models\UserPointsTransaction;

class VendorOrderStageObserver
{
    /**
     * Handle the VendorOrderStage "updated" event.
     */
    public function updated(VendorOrderStage $vendorOrderStage): void
    {
        // Check if stage_id was changed
        if ($vendorOrderStage->isDirty('stage_id')) {
            $this->handleStageChange($vendorOrderStage);
        }
    }

    /**
     * Handle stage change for vendor order
     */
    protected function handleStageChange(VendorOrderStage $vendorOrderStage): void
    {
        $newStage = OrderStage::withoutGlobalScopes()->find($vendorOrderStage->stage_id);
        
        if (!$newStage) {
            return;
        }

        switch ($newStage->type) {
            case 'deliver':
                // Vendor order delivered - fulfill bookings and award points
                $this->fulfillVendorBookings($vendorOrderStage);
                $this->awardPointsForVendorOrder($vendorOrderStage);
                break;

            case 'cancel':
                // Vendor order cancelled - release bookings
                $this->releaseVendorBookings($vendorOrderStage);
                break;
        }
    }

    /**
     * Fulfill stock bookings for vendor's products
     */
    protected function fulfillVendorBookings(VendorOrderStage $vendorOrderStage): void
    {
        // Get order product IDs for this vendor
        $orderProductIds = OrderProduct::where('order_id', $vendorOrderStage->order_id)
            ->where('vendor_id', $vendorOrderStage->vendor_id)
            ->pluck('id');

        if ($orderProductIds->isEmpty()) {
            return;
        }

        $bookings = StockBooking::where('order_id', $vendorOrderStage->order_id)
            ->whereIn('order_product_id', $orderProductIds)
            ->whereIn('status', [StockBooking::STATUS_BOOKED, StockBooking::STATUS_ALLOCATED])
            ->get();

        foreach ($bookings as $booking) {
            $booking->update([
                'status' => StockBooking::STATUS_FULFILLED,
                'fulfilled_at' => now(),
            ]);

            Log::info('Stock booking fulfilled', [
                'booking_id' => $booking->id,
                'order_id' => $vendorOrderStage->order_id,
                'vendor_id' => $vendorOrderStage->vendor_id,
            ]);
        }
    }

    /**
     * Release stock bookings for vendor's products
     */
    protected function releaseVendorBookings(VendorOrderStage $vendorOrderStage): void
    {
        // Get order product IDs for this vendor
        $orderProductIds = OrderProduct::where('order_id', $vendorOrderStage->order_id)
            ->where('vendor_id', $vendorOrderStage->vendor_id)
            ->pluck('id');

        if ($orderProductIds->isEmpty()) {
            return;
        }

        $bookings = StockBooking::where('order_id', $vendorOrderStage->order_id)
            ->whereIn('order_product_id', $orderProductIds)
            ->whereIn('status', [StockBooking::STATUS_BOOKED, StockBooking::STATUS_ALLOCATED])
            ->get();

        foreach ($bookings as $booking) {
            $booking->update([
                'status' => StockBooking::STATUS_RELEASED,
                'released_at' => now(),
            ]);

            Log::info('Stock booking released', [
                'booking_id' => $booking->id,
                'order_id' => $vendorOrderStage->order_id,
                'vendor_id' => $vendorOrderStage->vendor_id,
            ]);
        }
    }

    /**
     * Award points to customer when vendor order is delivered
     */
    protected function awardPointsForVendorOrder(VendorOrderStage $vendorOrderStage): void
    {
        try {
            // Check if points system is enabled
            $pointsSystem = PointsSystem::latest()->first();
            if (!$pointsSystem || !$pointsSystem->is_enabled) {
                Log::info('Points system is disabled, skipping points award', [
                    'order_id' => $vendorOrderStage->order_id,
                    'vendor_id' => $vendorOrderStage->vendor_id
                ]);
                return;
            }

            $order = $vendorOrderStage->order;
            if (!$order) {
                Log::warning('No order found for vendor order stage', [
                    'vendor_order_stage_id' => $vendorOrderStage->id
                ]);
                return;
            }

            // Get customer ID
            $customerId = $order->customer_id;
            if (!$customerId) {
                Log::warning('No customer ID found for order', ['order_id' => $order->id]);
                return;
            }

            // Get order's country currency
            $country = $order->country;
            if (!$country || !$country->currency) {
                Log::warning('No currency found for order country', ['order_id' => $order->id]);
                return;
            }

            $currencyId = $country->currency->id;

            // Get order products for this vendor
            $orderProducts = OrderProduct::where('order_id', $vendorOrderStage->order_id)
                ->where('vendor_id', $vendorOrderStage->vendor_id)
                ->get();

            if ($orderProducts->isEmpty()) {
                Log::info('No order products found for vendor', [
                    'order_id' => $vendorOrderStage->order_id,
                    'vendor_id' => $vendorOrderStage->vendor_id
                ]);
                return;
            }

            // Calculate total points from all products
            $totalPoints = 0;
            foreach ($orderProducts as $orderProduct) {
                // Calculate points based on product price (price already includes quantity)
                $productPoints = PointsHelper::calculatePointsByCurrency(
                    (float) $orderProduct->price,
                    $currencyId
                );
                $totalPoints += $productPoints;
            }

            if ($totalPoints <= 0) {
                Log::info('No points to award (amount too low)', [
                    'order_id' => $order->id,
                    'vendor_id' => $vendorOrderStage->vendor_id
                ]);
                return;
            }

            // Get or create user points record
            $userPoints = UserPoints::firstOrCreate(
                ['user_id' => $customerId],
                [
                    'total_points' => 0,
                    'earned_points' => 0,
                    'redeemed_points' => 0,
                    'expired_points' => 0,
                ]
            );

            // Update points
            $userPoints->total_points += $totalPoints;
            $userPoints->earned_points += $totalPoints;
            $userPoints->save();

            // Create transaction record
            $vendorName = $vendorOrderStage->vendor->name ?? 'Vendor';
            $transaction = UserPointsTransaction::create([
                'user_id' => $customerId,
                'points' => $totalPoints,
                'type' => 'earned',
                'transactionable_type' => VendorOrderStage::class,
                'transactionable_id' => $vendorOrderStage->id,
            ]);

            // Set transaction descriptions
            $transaction->setTranslation('description', 'en', "Earned {$totalPoints} points from order #{$order->order_number} ({$vendorName})");
            $transaction->setTranslation('description', 'ar', "حصلت على {$totalPoints} نقطة من الطلب #{$order->order_number} ({$vendorName})");
            $transaction->save();

            Log::info('Points awarded for delivered vendor order', [
                'order_id' => $order->id,
                'vendor_id' => $vendorOrderStage->vendor_id,
                'customer_id' => $customerId,
                'points_awarded' => $totalPoints,
                'new_total_points' => $userPoints->total_points
            ]);

        } catch (\Exception $e) {
            Log::error('Error awarding points for vendor order', [
                'order_id' => $vendorOrderStage->order_id,
                'vendor_id' => $vendorOrderStage->vendor_id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
