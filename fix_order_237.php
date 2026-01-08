<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get order 237
$order = \Modules\Order\app\Models\Order::find(237);

if (!$order) {
    echo "Order 237 not found\n";
    exit(1);
}

// Get deliver stage
$deliverStage = \Modules\Order\app\Models\OrderStage::where('type', 'deliver')->first();

if (!$deliverStage) {
    echo "Deliver stage not found\n";
    exit(1);
}

// Get accounting service
$service = app(\Modules\Accounting\app\Services\AccountingService::class);

// Process the order
$service->processOrderStageChange($order, $deliverStage);

echo "Successfully recreated accounting entry for order 237\n";

// Show the new entry
$entry = \Modules\Accounting\app\Models\AccountingEntry::where('order_id', 237)->first();
if ($entry) {
    echo "New amount: " . $entry->amount . "\n";
    echo "Commission: " . $entry->commission_amount . "\n";
    echo "Vendor amount: " . $entry->vendor_amount . "\n";
}
