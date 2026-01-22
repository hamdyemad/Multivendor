<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$refund = \Modules\Refund\app\Models\RefundRequest::find(77);

if ($refund) {
    echo "Refund #77 Details:\n";
    echo "==================\n";
    echo "Order ID: {$refund->order_id}\n";
    echo "Vendor ID: {$refund->vendor_id}\n";
    echo "Status: {$refund->status}\n";
    echo "Created: {$refund->created_at}\n";
    echo "Items Count: {$refund->items->count()}\n\n";
    
    echo "Items:\n";
    foreach ($refund->items as $item) {
        echo "  - Order Product #{$item->order_product_id}: Qty {$item->quantity}\n";
    }
    
    echo "\n";
    echo "Total Refund Amount: {$refund->total_refund_amount} EGP\n";
} else {
    echo "Refund #77 not found\n";
}
