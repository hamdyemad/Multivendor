<?php

namespace Modules\Order\app\Observers;

use Illuminate\Support\Facades\Log;
use Modules\Order\app\Models\OrderProduct;
use Modules\Order\app\Models\OrderStage;
use Modules\Order\app\Models\VendorOrderStage;
use Modules\Order\app\Models\Order;
use App\Services\AdminNotificationService;

class OrderProductObserver
{
    public function __construct(
        protected AdminNotificationService $notificationService
    ) {}

    /**
     * Handle the OrderProduct "created" event.
     * Create vendor order stage if it doesn't exist for this vendor.
     * Also create order notifications after first product is added.
     */
    public function created(OrderProduct $orderProduct): void
    {
        $this->ensureVendorStageExists($orderProduct);
        $this->createOrderNotificationsIfNeeded($orderProduct);
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
    
    /**
     * Create order notifications if this is the first product being added
     */
    protected function createOrderNotificationsIfNeeded(OrderProduct $orderProduct): void
    {
        if (!$orderProduct->order_id) {
            return;
        }
        
        // Check if notifications already exist for this order
        $existingNotifications = \App\Models\AdminNotification::where('notifiable_type', Order::class)
            ->where('notifiable_id', $orderProduct->order_id)
            ->where('type', 'new_order')
            ->exists();
        
        if ($existingNotifications) {
            // Notifications already created
            return;
        }
        
        // Get the order
        $order = Order::find($orderProduct->order_id);
        if (!$order) {
            return;
        }
        
        // Create notifications
        $this->createOrderNotification($order);
        
        Log::info('Order notifications created from OrderProductObserver', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);
    }
    
    /**
     * Create admin notification for new order
     */
    protected function createOrderNotification(Order $order): void
    {
        // Get all vendors involved in this order
        $vendorIds = $order->products()->distinct()->pluck('vendor_id')->toArray();
        
        // Log for debugging
        Log::info('Order Notification Debug', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'vendor_ids' => $vendorIds,
            'has_products' => $order->products()->count(),
        ]);
        
        // If no vendors yet, skip notification
        if (empty($vendorIds)) {
            Log::warning('Order has no vendors/products', ['order_id' => $order->id]);
            return;
        }
        
        // Get customer name
        $customerName = $order->customer_name ?? ($order->customer ? $order->customer->name : trans('common.guest'));
        
        // Create notification for each vendor
        foreach ($vendorIds as $vendorId) {
            if (!$vendorId) continue;
            
            $this->notificationService->create(
                type: 'new_order',
                title: 'menu.order', // Translation key
                description: 'order.new_order_received', // Translation key
                url: $this->notificationService->generateAdminUrl('admin.orders.show', ['order' => $order->id]),
                icon: 'uil-shopping-bag',
                color: 'primary',
                notifiable: $order,
                data: [
                    'order.order_number' => $order->order_number,
                    'common.name' => $customerName,
                ],
                vendorId: $vendorId
            );
        }
        
        // Create notification for admin
        $this->notificationService->create(
            type: 'new_order',
            title: 'menu.order', // Translation key
            description: 'order.new_order_received', // Translation key
            url: $this->notificationService->generateAdminUrl('admin.orders.show', ['order' => $order->id]),
            icon: 'uil-shopping-bag',
            color: 'primary',
            notifiable: $order,
            data: [
                'order.order_number' => $order->order_number,
                'common.name' => $customerName,
            ],
            vendorId: null // Admin notification
        );
    }
}
