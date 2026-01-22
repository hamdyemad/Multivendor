<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "═══════════════════════════════════════════════════════════════\n";
echo "         Vendor Dashboard Calculation Test                     \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Test for Vendor #107 (Value)
$vendorId = 107;
$vendor = \Modules\Vendor\app\Models\Vendor::find($vendorId);

if (!$vendor) {
    echo "ERROR: Vendor #{$vendorId} not found!\n";
    exit;
}

echo "Testing for Vendor: {$vendor->name} (#{$vendorId})\n\n";

// Get delivered stage
$deliveredStage = \Modules\Order\app\Models\OrderStage::withoutCountryFilter()
    ->where('type', 'deliver')
    ->first();

// Get delivered orders for this vendor
$deliveredOrders = \Modules\Order\app\Models\VendorOrderStage::where('stage_id', $deliveredStage->id)
    ->where('vendor_id', $vendorId)
    ->whereHas('order')
    ->with(['order.products', 'order.products.taxes'])
    ->get();

echo "DELIVERED ORDERS FOR THIS VENDOR:\n";
echo str_repeat("─", 63) . "\n";

$totalIncome = 0;

foreach ($deliveredOrders as $vendorStage) {
    $order = $vendorStage->order;
    
    echo "Order #{$order->id} ({$order->order_number})\n";
    
    // Get vendor products
    $vendorProducts = $order->products->where('vendor_id', $vendorId);
    
    $productsTotal = $vendorProducts->sum('price');
    $shippingTotal = $vendorProducts->sum('shipping_cost');
    
    echo "  Products: " . number_format($productsTotal, 2) . " EGP\n";
    echo "  Shipping: " . number_format($shippingTotal, 2) . " EGP\n";
    
    // Get fees and discounts
    $vendorFees = \Modules\Order\app\Models\OrderExtraFeeDiscount::where('order_id', $order->id)
        ->where('vendor_id', $vendorId)
        ->where('type', 'fee')
        ->sum('cost') ?? 0;
    
    $vendorDiscounts = \Modules\Order\app\Models\OrderExtraFeeDiscount::where('order_id', $order->id)
        ->where('vendor_id', $vendorId)
        ->where('type', 'discount')
        ->sum('cost') ?? 0;
    
    echo "  Fees: " . number_format($vendorFees, 2) . " EGP\n";
    echo "  Discounts: " . number_format($vendorDiscounts, 2) . " EGP\n";
    
    $vendorTotal = $productsTotal + $shippingTotal + $vendorFees - $vendorDiscounts;
    
    echo "  Total: " . number_format($vendorTotal, 2) . " EGP\n\n";
    
    $totalIncome += $vendorTotal;
}

echo "Total Income for Vendor: " . number_format($totalIncome, 2) . " EGP\n\n";

// Get refunds for this vendor
echo "REFUNDED REQUESTS FOR THIS VENDOR:\n";
echo str_repeat("─", 63) . "\n";

$refundedRequests = \Modules\Refund\app\Models\RefundRequest::where('status', 'refunded')
    ->where('vendor_id', $vendorId)
    ->get();

$totalRefunds = 0;

foreach ($refundedRequests as $refund) {
    echo "Refund #{$refund->id} ({$refund->refund_number})\n";
    
    $vendorDeduction = $refund->total_products_amount 
        + $refund->total_shipping_amount 
        + ($refund->vendor_fees_amount ?? 0)
        - ($refund->vendor_discounts_amount ?? 0)
        - ($refund->return_shipping_cost ?? 0);
    
    echo "  Products: {$refund->total_products_amount} EGP\n";
    echo "  Shipping: {$refund->total_shipping_amount} EGP\n";
    echo "  Fees: " . ($refund->vendor_fees_amount ?? 0) . " EGP\n";
    echo "  Discounts: " . ($refund->vendor_discounts_amount ?? 0) . " EGP\n";
    echo "  Vendor Deduction: " . number_format($vendorDeduction, 2) . " EGP\n\n";
    
    $totalRefunds += $vendorDeduction;
}

echo "Total Refunds for Vendor: " . number_format($totalRefunds, 2) . " EGP\n\n";

// Calculate net income
$netIncome = $totalIncome - $totalRefunds;

echo "═══════════════════════════════════════════════════════════════\n";
echo "VENDOR TOTALS:\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "Total Income: " . number_format($totalIncome, 2) . " EGP\n";
echo "Total Refunds: " . number_format($totalRefunds, 2) . " EGP\n";
echo "Net Income: " . number_format($netIncome, 2) . " EGP\n\n";

// Test the DashboardService
echo "TESTING DASHBOARD SERVICE:\n";
echo str_repeat("─", 63) . "\n";

// Create service instance and manually set vendor properties
$dashboardService = new \App\Services\DashboardService();

// Use reflection to set private properties
$reflection = new \ReflectionClass($dashboardService);

$isVendorProp = $reflection->getProperty('isVendor');
$isVendorProp->setAccessible(true);
$isVendorProp->setValue($dashboardService, true);

$vendorIdProp = $reflection->getProperty('vendorId');
$vendorIdProp->setAccessible(true);
$vendorIdProp->setValue($dashboardService, $vendorId);

echo "Testing with vendor ID: {$vendorId}\n\n";

$dashboardData = $dashboardService->getDashboardData('eg');
$salesOverview = $dashboardData['salesOverview'] ?? [];

echo "Dashboard Service Results:\n";
echo "  Total Income (After Delivery): " . number_format($salesOverview['total_income'] ?? 0, 2) . " EGP\n";
echo "  Net Profit Y.T.D: " . number_format($salesOverview['net_profit_ytd'] ?? 0, 2) . " EGP\n\n";

if (abs(($salesOverview['total_income'] ?? 0) - $netIncome) < 0.01) {
    echo "✅ Dashboard Service matches manual calculation!\n";
} else {
    echo "❌ Dashboard Service MISMATCH!\n";
    echo "   Expected: " . number_format($netIncome, 2) . " EGP\n";
    echo "   Got: " . number_format($salesOverview['total_income'] ?? 0, 2) . " EGP\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "                    Test Complete!                             \n";
echo "═══════════════════════════════════════════════════════════════\n";
