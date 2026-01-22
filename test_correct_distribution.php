<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\Order\app\Models\Order;

echo "=== Testing Correct Distribution Method ===\n\n";

$order = Order::with(['products'])->find(254);

if (!$order) {
    echo "❌ Order #254 not found\n";
    exit(1);
}

$totalPromo = $order->customer_promo_code_amount ?? 0;
$totalPoints = $order->points_cost ?? 0;
$totalDiscounts = $totalPromo + $totalPoints;

echo "Order Totals:\n";
echo "=============\n";
echo "Total Promo: " . number_format($totalPromo, 2) . " EGP\n";
echo "Total Points: " . number_format($totalPoints, 2) . " EGP\n";
echo "Total Discounts: " . number_format($totalDiscounts, 2) . " EGP\n\n";

// Calculate order grand total (products + shipping)
$orderGrandTotal = $order->products->sum(function($p) {
    return $p->price + ($p->shipping_cost ?? 0);
});

echo "Order Grand Total (products + shipping): " . number_format($orderGrandTotal, 2) . " EGP\n\n";

// Group by vendor
$productsByVendor = $order->products->groupBy(function ($product) {
    return $product->vendorProduct?->vendor_id;
});

echo "METHOD 1: Distribute based on products only (WRONG)\n";
echo "====================================================\n\n";

$orderTotalProducts = $order->products->sum('price');
$sumCustomerTotal1 = 0;

foreach ($productsByVendor as $vendorId => $vendorProducts) {
    $vendorName = $vendorProducts->first()->vendorProduct?->vendor?->name ?? 'N/A';
    $vendorSubtotal = $vendorProducts->sum('price');
    $vendorShipping = $vendorProducts->sum('shipping_cost');
    
    // Distribute based on products only
    $vendorPercentage = $orderTotalProducts > 0 ? ($vendorSubtotal / $orderTotalProducts) : 0;
    $vendorPromo = $totalPromo * $vendorPercentage;
    $vendorPoints = $totalPoints * $vendorPercentage;
    
    $customerTotal = $vendorSubtotal - $vendorPromo - $vendorPoints + $vendorShipping;
    $sumCustomerTotal1 += $customerTotal;
    
    echo "Vendor: $vendorName\n";
    echo "  Subtotal: " . number_format($vendorSubtotal, 2) . " EGP\n";
    echo "  Shipping: " . number_format($vendorShipping, 2) . " EGP\n";
    echo "  Percentage (products): " . number_format($vendorPercentage * 100, 2) . "%\n";
    echo "  Promo: -" . number_format($vendorPromo, 2) . " EGP\n";
    echo "  Points: -" . number_format($vendorPoints, 2) . " EGP\n";
    echo "  Customer Total: " . number_format($customerTotal, 2) . " EGP\n\n";
}

echo "Sum of Customer Totals: " . number_format($sumCustomerTotal1, 2) . " EGP (should be 0.00)\n";
echo "Error: " . number_format($sumCustomerTotal1, 2) . " EGP ❌\n\n";

echo "METHOD 2: Distribute based on grand total (products + shipping) (CORRECT)\n";
echo "==========================================================================\n\n";

$sumCustomerTotal2 = 0;

foreach ($productsByVendor as $vendorId => $vendorProducts) {
    $vendorName = $vendorProducts->first()->vendorProduct?->vendor?->name ?? 'N/A';
    $vendorSubtotal = $vendorProducts->sum('price');
    $vendorShipping = $vendorProducts->sum('shipping_cost');
    $vendorGrandTotal = $vendorSubtotal + $vendorShipping;
    
    // Distribute based on grand total (products + shipping)
    $vendorPercentage = $orderGrandTotal > 0 ? ($vendorGrandTotal / $orderGrandTotal) : 0;
    $vendorPromo = $totalPromo * $vendorPercentage;
    $vendorPoints = $totalPoints * $vendorPercentage;
    
    $customerTotal = $vendorSubtotal - $vendorPromo - $vendorPoints + $vendorShipping;
    $sumCustomerTotal2 += $customerTotal;
    
    echo "Vendor: $vendorName\n";
    echo "  Subtotal: " . number_format($vendorSubtotal, 2) . " EGP\n";
    echo "  Shipping: " . number_format($vendorShipping, 2) . " EGP\n";
    echo "  Grand Total: " . number_format($vendorGrandTotal, 2) . " EGP\n";
    echo "  Percentage (grand total): " . number_format($vendorPercentage * 100, 2) . "%\n";
    echo "  Promo: -" . number_format($vendorPromo, 2) . " EGP\n";
    echo "  Points: -" . number_format($vendorPoints, 2) . " EGP\n";
    echo "  Customer Total: " . number_format($customerTotal, 2) . " EGP\n\n";
}

echo "Sum of Customer Totals: " . number_format($sumCustomerTotal2, 2) . " EGP (should be 0.00)\n";

if (abs($sumCustomerTotal2) < 0.01) {
    echo "✅ CORRECT! Error is negligible (< 0.01 EGP)\n";
} else {
    echo "Error: " . number_format($sumCustomerTotal2, 2) . " EGP\n";
}
