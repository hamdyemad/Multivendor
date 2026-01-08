<?php

namespace Modules\Order\app\Observers;

use Illuminate\Support\Facades\Log;
use Modules\CatalogManagement\app\Models\StockBooking;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderStage;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     * Vendor order stages are now created by OrderProductObserver when products are added.
     */
    public function created(Order $order): void
    {
        // Vendor order stages are handled by OrderProductObserver
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
                // Order delivered - fulfill all bookings
                // Points are now awarded per vendor via VendorOrderStageObserver
                if ($bookings->isNotEmpty()) {
                    $this->fulfillBookings($bookings, $order);
                }
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
