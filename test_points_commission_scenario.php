<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "═══════════════════════════════════════════════════════════════\n";
echo "    Points/Promo Commission Scenario Test                      \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "SCENARIO:\n";
echo "1. Create order with points/promo code\n";
echo "2. Commission should be calculated normally (NOT 0%)\n";
echo "3. Create refund request\n";
echo "4. Commission should be refunded correctly\n\n";

echo str_repeat("─", 63) . "\n\n";

// Test with existing Order #254
$orderId = 254;
$order = \Modules\Order\app\Models\Order::with(['products'])->find($orderId);

if (!$order) {
    echo "Order #{$orderId} not found!\n";
    exit;
}

echo "STEP 1: Check Order #{$orderId}\n";
echo str_repeat("─", 63) . "\n";
echo "Order Number: {$order->order_number}\n";
echo "Points Used: {$order->points_used} EGP\n";
echo "Promo Code: {$order->promo_code_amount} EGP\n";
echo "Customer Paid: {$order->customer_paid} EGP\n\n";

echo "Products:\n";
$hasZeroCommission = false;
$totalExpectedCommission = 0;

foreach ($order->products as $product) {
    $vendor = \Modules\Vendor\app\Models\Vendor::find($product->vendor_id);
    $vendorProduct = $product->vendorProduct;
    $department = $vendorProduct->product->subCategory->category->department ?? null;
    $departmentCommission = $department ? $department->commission : 0;
    
    echo "  Product #{$product->id}: {$vendorProduct->product->name}\n";
    echo "    Vendor: {$vendor->name}\n";
    echo "    Price + Shipping: " . ($product->price + $product->shipping_cost) . " EGP\n";
    echo "    Stored Commission: {$product->commission}%\n";
    echo "    Department Commission: {$departmentCommission}%\n";
    
    if ($product->commission == 0 && $departmentCommission > 0) {
        echo "    ❌ PROBLEM: Commission is 0% but should be {$departmentCommission}%\n";
        $hasZeroCommission = true;
    } else if ($product->commission == $departmentCommission) {
        echo "    ✅ OK: Commission matches department\n";
    }
    
    $expectedCommission = (($product->price + $product->shipping_cost) * $departmentCommission) / 100;
    $totalExpectedCommission += $expectedCommission;
    
    echo "\n";
}

if ($hasZeroCommission) {
    echo "⚠️  ISSUE DETECTED: Some products have 0% commission\n";
    echo "This happens because AdjustCommissionForPoints pipeline sets commission to 0\n";
    echo "when points are used. This is WRONG!\n\n";
    echo "SOLUTION: Remove AdjustCommissionForPoints from the pipeline\n\n";
} else {
    echo "✅ All products have correct commission!\n\n";
}

echo "Total Expected Commission: " . number_format($totalExpectedCommission, 2) . " EGP\n\n";

// Test refund scenario
echo "STEP 2: Test Refund Scenario\n";
echo str_repeat("─", 63) . "\n";

// Get a product to refund
$productToRefund = $order->products->first();

if (!$productToRefund) {
    echo "No products found to refund\n";
    exit;
}

echo "Simulating refund for Product #{$productToRefund->id}\n";
echo "  Quantity: 1 (out of {$productToRefund->quantity})\n";
echo "  Price: {$productToRefund->price} EGP\n";
echo "  Shipping: {$productToRefund->shipping_cost} EGP\n";
echo "  Commission: {$productToRefund->commission}%\n\n";

// Calculate refund commission
$refundAmount = $productToRefund->price + $productToRefund->shipping_cost;
$refundCommission = ($refundAmount * $productToRefund->commission) / 100;

echo "Refund Calculations:\n";
echo "  Refund Amount: " . number_format($refundAmount, 2) . " EGP\n";
echo "  Refund Commission: " . number_format($refundCommission, 2) . " EGP\n";

if ($productToRefund->commission == 0) {
    echo "  ❌ PROBLEM: Commission is 0%, so refund commission will be 0\n";
    echo "  This means vendor won't get commission back on refund!\n";
} else {
    echo "  ✅ OK: Commission will be calculated correctly on refund\n";
}

echo "\n";

// Check if order is delivered
$deliveredStage = \Modules\Order\app\Models\OrderStage::withoutCountryFilter()
    ->where('type', 'deliver')
    ->first();

$vendorStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $orderId)
    ->where('vendor_id', $productToRefund->vendor_id)
    ->first();

if ($vendorStage && $vendorStage->stage_id == $deliveredStage->id) {
    echo "✅ Order is delivered, refund can be created\n\n";
} else {
    echo "⚠️  Order is not delivered yet (Stage: {$vendorStage->stage->name})\n";
    echo "Refund can only be created after delivery\n\n";
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "SUMMARY:\n";
echo "═══════════════════════════════════════════════════════════════\n";

if ($hasZeroCommission) {
    echo "❌ FAILED: Commission is 0% for orders with points/promo\n";
    echo "\nRECOMMENDATION:\n";
    echo "1. Remove AdjustCommissionForPoints pipeline from OrderApiService\n";
    echo "2. Create new orders to test\n";
    echo "3. Commission should be calculated normally even with points\n";
} else {
    echo "✅ PASSED: Commission is calculated correctly\n";
    echo "✅ Refunds will work correctly with proper commission\n";
}

echo "\n";
