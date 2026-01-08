<?php

namespace Modules\Order\app\Observers;

use Modules\Order\app\Models\Order;

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
     * Stock bookings and points are now handled at vendor level via VendorOrderStageObserver
     */
    public function updated(Order $order): void
    {
        // Stage changes are handled at vendor level via VendorOrderStageObserver
    }
}
