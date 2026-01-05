<?php

namespace Modules\Order\app\Observers;

use Illuminate\Support\Facades\Log;
use Modules\Order\app\Models\OrderProduct;
use Modules\Order\app\Models\OrderStage;
use Modules\Order\app\Models\VendorOrderStage;

class OrderProductObserver
{
    /**
     * Handle the OrderProduct "created" event.
     * Create vendor order stage if it doesn't exist for this vendor.
     */
    public function created(OrderProduct $orderProduct): void
    {
        $this->ensureVendorStageExists($orderProduct);
    }

    /**
     * Ensure vendor order stage exists for the vendor of this product
     */
    protected function ensureVendorStageExists(OrderProduct $orderProduct): void
    {
        // Skip if no vendor_id
        if (!$orderProduct->vendor_id || !$orderProduct->order_id) {
            return;
        }

        // Check if vendor stage already exists for this order and vendor
        $existingStage = VendorOrderStage::where('order_id', $orderProduct->order_id)
            ->where('vendor_id', $orderProduct->vendor_id)
            ->first();

        if ($existingStage) {
            // Stage already exists, no need to create
            return;
        }

        // Get the default "new" stage
        $defaultStage = OrderStage::withoutGlobalScopes()
            ->where('type', 'new')
            ->first();

        if (!$defaultStage) {
            Log::warning('No default "new" stage found for vendor order stages', [
                'order_id' => $orderProduct->order_id,
                'vendor_id' => $orderProduct->vendor_id,
            ]);
            return;
        }

        // Create vendor order stage
        VendorOrderStage::create([
            'order_id' => $orderProduct->order_id,
            'vendor_id' => $orderProduct->vendor_id,
            'stage_id' => $defaultStage->id,
        ]);

        Log::info('Vendor order stage created from OrderProduct', [
            'order_id' => $orderProduct->order_id,
            'vendor_id' => $orderProduct->vendor_id,
            'stage_id' => $defaultStage->id,
            'order_product_id' => $orderProduct->id,
        ]);
    }
}
