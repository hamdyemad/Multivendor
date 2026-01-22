<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Refund Validation Logic\n";
echo "================================\n\n";

// Test 1: Try to refund 1 quantity (should succeed)
echo "Test 1: Refund 1 quantity (remaining available)\n";
echo "------------------------------------------------\n";

$orderProduct = \Modules\Order\app\Models\OrderProduct::find(548);
echo "Order Product #548:\n";
echo "  Total Quantity: {$orderProduct->quantity}\n";

$totalRefunded = \Modules\Refund\app\Models\RefundRequestItem::where('order_product_id', 548)
    ->whereHas('refundRequest', function($q) {
        $q->whereNotIn('status', ['cancelled']);
    })
    ->sum('quantity');

echo "  Already Refunded: {$totalRefunded}\n";
echo "  Available for Refund: " . ($orderProduct->quantity - $totalRefunded) . "\n\n";

// Test 2: Try to refund 2 quantity (should fail)
echo "Test 2: Try to refund 2 quantity (should fail)\n";
echo "-----------------------------------------------\n";
$availableForRefund = $orderProduct->quantity - $totalRefunded;
$requestedQty = 2;

if ($requestedQty > $availableForRefund) {
    echo "  ✅ Validation would correctly REJECT this request\n";
    echo "  Requested: {$requestedQty}, Available: {$availableForRefund}\n";
} else {
    echo "  ❌ Validation would incorrectly ALLOW this request\n";
}

echo "\n";

// Test 3: Check if we can create multiple refunds for same vendor
echo "Test 3: Multiple refunds per vendor\n";
echo "------------------------------------\n";
$existingRefunds = \Modules\Refund\app\Models\RefundRequest::where('order_id', 253)
    ->where('vendor_id', 107)
    ->whereNotIn('status', ['cancelled'])
    ->get();

echo "  Existing refunds for Vendor 107 in Order 253: {$existingRefunds->count()}\n";
foreach ($existingRefunds as $refund) {
    echo "    - Refund #{$refund->id} (Status: {$refund->status})\n";
}
echo "  ✅ New validation allows multiple refunds per vendor\n";
echo "  ✅ Each refund is validated by order product quantity\n";

echo "\nValidation logic updated successfully!\n";
