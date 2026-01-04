<?php

namespace Modules\Order\app\Observers;

use Illuminate\Support\Facades\Log;
use Modules\CatalogManagement\app\Models\StockBooking;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderStage;
use Modules\SystemSetting\app\Models\PointsSetting;
use Modules\SystemSetting\app\Models\PointsSystem;
use Modules\SystemSetting\app\Models\UserPoints;
use Modules\SystemSetting\app\Models\UserPointsTransaction;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     * Create vendor order stages for each vendor in the order
     */
    public function created(Order $order): void
    {
        // Get unique vendor IDs from order products
        $vendorIds = $order->products()->distinct()->pluck('vendor_id');
        
        // Get the default "new" stage
        $defaultStage = OrderStage::withoutGlobalScopes()
            ->where('type', 'new')
            ->first();
        
        if (!$defaultStage) {
            Log::warning('No default "new" stage found for vendor order stages', ['order_id' => $order->id]);
            return;
        }
        
        // Create vendor order stage for each vendor
        foreach ($vendorIds as $vendorId) {
            \Modules\Order\app\Models\VendorOrderStage::create([
                'order_id' => $order->id,
                'vendor_id' => $vendorId,
                'stage_id' => $defaultStage->id,
            ]);
            
            Log::info('Vendor order stage created', [
                'order_id' => $order->id,
                'vendor_id' => $vendorId,
                'stage_id' => $defaultStage->id,
            ]);
        }
    }

    /**
     * Handle the Order "updated" event.
     * Check if stage changed and update stock bookings accordingly
     */
    public function updated(Order $order): void
    {
        // Check if stage_id was changed
        if ($order->isDirty('stage_id')) {
            $this->handleStageChange($order);
        }
    }

    /**
     * Handle stage change and update stock bookings
     */
    protected function handleStageChange(Order $order): void
    {
        $newStage = OrderStage::withoutGlobalScopes()->find($order->stage_id);
        
        if (!$newStage) {
            return;
        }

        $stageType = $newStage->type;

        // Get all booked stock bookings for this order
        $bookings = StockBooking::where('order_id', $order->id)->get();

        switch ($stageType) {
            case 'deliver':
                // Order delivered - fulfill all bookings and award points
                if ($bookings->isNotEmpty()) {
                    $this->fulfillBookings($bookings, $order);
                }
                $this->awardPointsForOrder($order);
                break;

            case 'cancel':
                // Order cancelled - release all bookings
                if ($bookings->isNotEmpty()) {
                    $this->releaseBookings($bookings, $order);
                }
                break;

            // For 'new', 'in_progress', etc. - keep as booked (no change needed)
        }
    }

    /**
     * Award points to customer when order is delivered
     */
    protected function awardPointsForOrder(Order $order): void
    {
        try {
            // Check if points system is enabled
            $pointsSystem = PointsSystem::latest()->first();
            if (!$pointsSystem || !$pointsSystem->is_enabled) {
                Log::info('Points system is disabled, skipping points award', ['order_id' => $order->id]);
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

            // Get points setting for this currency
            $pointsSetting = PointsSetting::where('currency_id', $currencyId)
                ->where('is_active', true)
                ->first();

            if (!$pointsSetting || $pointsSetting->points_value <= 0) {
                Log::info('No active points setting for currency', [
                    'order_id' => $order->id,
                    'currency_id' => $currencyId
                ]);
                return;
            }

            // Calculate points to award based on total_price (full order total)
            // points_value = points per 1 currency unit
            $orderAmount = (float) $order->total_price;
            $pointsToAward = floor($orderAmount * $pointsSetting->points_value);

            if ($pointsToAward <= 0) {
                Log::info('No points to award (amount too low)', [
                    'order_id' => $order->id,
                    'order_amount' => $orderAmount
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
            $userPoints->total_points += $pointsToAward;
            $userPoints->earned_points += $pointsToAward;
            $userPoints->save();

            // Create transaction record
            $transaction = UserPointsTransaction::create([
                'user_id' => $customerId,
                'points' => $pointsToAward,
                'type' => 'earned',
                'transactionable_type' => Order::class,
                'transactionable_id' => $order->id,
            ]);

            // Set transaction descriptions
            $transaction->setTranslation('description', 'en', "Earned {$pointsToAward} points from order #{$order->order_number}");
            $transaction->setTranslation('description', 'ar', "حصلت على {$pointsToAward} نقطة من الطلب #{$order->order_number}");
            $transaction->save();

            Log::info('Points awarded for delivered order', [
                'order_id' => $order->id,
                'customer_id' => $customerId,
                'order_amount' => $orderAmount,
                'points_awarded' => $pointsToAward,
                'new_total_points' => $userPoints->total_points
            ]);

        } catch (\Exception $e) {
            Log::error('Error awarding points for order', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Fulfill all bookings (order delivered)
     */
    protected function fulfillBookings($bookings, Order $order): void
    {
        foreach ($bookings as $booking) {
            // Only fulfill if currently booked or allocated
            if (in_array($booking->status, [StockBooking::STATUS_BOOKED, StockBooking::STATUS_ALLOCATED])) {
                $booking->update([
                    'status' => StockBooking::STATUS_FULFILLED,
                    'fulfilled_at' => now(),
                ]);

                Log::info('Stock booking fulfilled', [
                    'booking_id' => $booking->id,
                    'order_id' => $order->id,
                ]);
            }
        }
    }

    /**
     * Release all bookings (order cancelled)
     */
    protected function releaseBookings($bookings, Order $order): void
    {
        foreach ($bookings as $booking) {
            // Only release if currently booked or allocated
            if (in_array($booking->status, [StockBooking::STATUS_BOOKED, StockBooking::STATUS_ALLOCATED])) {
                $booking->update([
                    'status' => StockBooking::STATUS_RELEASED,
                    'released_at' => now(),
                ]);

                Log::info('Stock booking released', [
                    'booking_id' => $booking->id,
                    'order_id' => $order->id,
                ]);
            }
        }
    }
}
