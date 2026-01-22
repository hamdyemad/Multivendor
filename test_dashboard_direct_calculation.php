<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "═══════════════════════════════════════════════════════════════\n";
echo "    Dashboard Direct Calculation Test (No Accounting)          \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Get delivered stage
$deliveredStage = \Modules\Order\app\Models\OrderStage::withoutCountryFilter()
    ->where('type', 'deliver')
    ->first();

if (!$deliveredStage) {
    echo "ERROR: Delivered stage not found!\n";
    exit;
}

echo "Delivered Stage ID: {$deliveredStage->id}\n\n";

// Get all delivered orders
$deliveredOrders = \Modules\Order\app\Models\VendorOrderStage::where('stage_id', $deliveredStage->id)
    ->whereHas('order')
    ->with(['order.products', 'order.products.taxes', 'vendor'])
    ->get();

echo "DELIVERED ORDERS:\n";
echo str_repeat("─", 63) . "\n";

$totalIncome = 0;

foreach ($deliveredOrders as $vendorStage) {
    $order = $vendorStage->order;
    $vendorId = $vendorStage->vendor_id;
    $vendor = $vendorStage->vendor;
    
    echo "Order #{$order->id} ({$order->order_number}) - Vendor: {$vendor->name}\n";
    
    // Get vendor products in this order
    $vendorProducts = $order->products->where('vendor_id', $vendorId);
    
    // Calculate vendor total
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

echo "Total Income from Delivered Orders: " . number_format($totalIncome, 2) . " EGP\n\n";

// Calculate refunds
echo "REFUNDED REQUESTS:\n";
echo str_repeat("─", 63) . "\n";

$refundedRequests = \Modules\Refund\app\Models\RefundRequest::where('status', 'refunded')
    ->with('vendor')
    ->get();

$totalRefunds = 0;

foreach ($refundedRequests as $refund) {
    echo "Refund #{$refund->id} ({$refund->refund_number}) - Vendor: {$refund->vendor->name}\n";
    
    // Calculate vendor deduction
    $vendorDeduction = $refund->total_products_amount 
        + $refund->total_shipping_amount 
        + ($refund->vendor_fees_amount ?? 0)
        - ($refund->vendor_discounts_amount ?? 0)
        - ($refund->return_shipping_cost ?? 0);
    
    echo "  Products: {$refund->total_products_amount} EGP\n";
    echo "  Shipping: {$refund->total_shipping_amount} EGP\n";
    echo "  Fees: " . ($refund->vendor_fees_amount ?? 0) . " EGP\n";
    echo "  Discounts: " . ($refund->vendor_discounts_amount ?? 0) . " EGP\n";
    echo "  Return Shipping: " . ($refund->return_shipping_cost ?? 0) . " EGP\n";
    echo "  Vendor Deduction: " . number_format($vendorDeduction, 2) . " EGP\n\n";
    
    $totalRefunds += $vendorDeduction;
}

echo "Total Refunds: " . number_format($totalRefunds, 2) . " EGP\n\n";

// Calculate net income
$netIncome = $totalIncome - $totalRefunds;

echo "═══════════════════════════════════════════════════════════════\n";
echo "FINAL TOTALS:\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "Total Income: " . number_format($totalIncome, 2) . " EGP\n";
echo "Total Refunds: " . number_format($totalRefunds, 2) . " EGP\n";
echo "Net Income: " . number_format($netIncome, 2) . " EGP\n\n";

// Compare with accounting entries
$accountingIncome = \Modules\Accounting\app\Models\AccountingEntry::where('type', 'income')->sum('amount');
$accountingRefunds = \Modules\Accounting\app\Models\AccountingEntry::where('type', 'refund')->sum('amount');
$accountingNet = $accountingIncome - $accountingRefunds;

echo "COMPARISON WITH ACCOUNTING ENTRIES:\n";
echo str_repeat("─", 63) . "\n";
echo "Accounting Income: " . number_format($accountingIncome, 2) . " EGP\n";
echo "Accounting Refunds: " . number_format($accountingRefunds, 2) . " EGP\n";
echo "Accounting Net: " . number_format($accountingNet, 2) . " EGP\n\n";

if (abs($totalIncome - $accountingIncome) < 0.01) {
    echo "✅ Income matches!\n";
} else {
    echo "❌ Income MISMATCH: Difference = " . number_format($totalIncome - $accountingIncome, 2) . " EGP\n";
}

if (abs($totalRefunds - $accountingRefunds) < 0.01) {
    echo "✅ Refunds match!\n";
} else {
    echo "❌ Refunds MISMATCH: Difference = " . number_format($totalRefunds - $accountingRefunds, 2) . " EGP\n";
}

if (abs($netIncome - $accountingNet) < 0.01) {
    echo "✅ Net Income matches!\n";
} else {
    echo "❌ Net Income MISMATCH: Difference = " . number_format($netIncome - $accountingNet, 2) . " EGP\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "                    Test Complete!                             \n";
echo "═══════════════════════════════════════════════════════════════\n";
