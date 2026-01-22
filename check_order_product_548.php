<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$orderProduct = \Modules\Order\app\Models\OrderProduct::find(548);

if ($orderProduct) {
    echo "Order Product #548 Details:\n";
    echo "===========================\n";
    echo "Order ID: {$orderProduct->order_id}\n";
    echo "Vendor ID: {$orderProduct->vendor_id}\n";
    echo "Total Quantity: {$orderProduct->quantity}\n";
    echo "Price: {$orderProduct->price} EGP\n\n";
    
    // Check existing refunds for this order product
    $refundItems = \Modules\Refund\app\Models\RefundRequestItem::where('order_product_id', 548)
        ->with('refundRequest')
        ->get();
    
    echo "Existing Refund Items:\n";
    $totalRefunded = 0;
    foreach ($refundItems as $item) {
        $status = $item->refundRequest->status;
        echo "  - Refund #{$item->refund_request_id} (Status: {$status}): Qty {$item->quantity}\n";
        if ($status !== 'cancelled') {
            $totalRefunded += $item->quantity;
        }
    }
    
    echo "\nTotal Refunded (excluding cancelled): {$totalRefunded}\n";
    echo "Remaining Available for Refund: " . ($orderProduct->quantity - $totalRefunded) . "\n";
} else {
    echo "Order Product #548 not found\n";
}
