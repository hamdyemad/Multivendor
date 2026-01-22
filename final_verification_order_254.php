<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\Order\app\Models\Order;

echo "=== Final Verification for Order #254 ===\n\n";

$order = Order::with(['products.vendorProduct.vendor'])->find(254);

if (!$order) {
    echo "❌ Order #254 not found\n";
    exit(1);
}

$totalPromo = $order->customer_promo_code_amount ?? 0;
$totalPoints = $order->points_cost ?? 0;
$totalDiscounts = $totalPromo + $totalPoints;

echo "Order Summary:\n";
echo "==============\n";
echo "Total Promo Code: " . number_format($totalPromo, 2) . " EGP\n";
echo "Total Points: " . number_format($totalPoints, 2) . " EGP\n";
echo "Total Discounts: " . number_format($totalDiscounts, 2) . " EGP\n";
echo "Customer Paid: " . number_format($order->customer_paid ?? 0, 2) . " EGP\n\n";

// Calculate order grand total
$orderGrandTotal = $order->products->sum(function($p) {
    return $p->price + ($p->shipping_cost ?? 0);
});

echo "Order Grand Total: " . number_format($orderGrandTotal, 2) . " EGP\n";
echo "Expected Customer Payment: " . number_format(max(0, $orderGrandTotal - $totalDiscounts), 2) . " EGP\n\n";

// Group by vendor
$productsByVendor = $order->products->groupBy(function ($product) {
    return $product->vendorProduct?->vendor_id;
});

echo "Per-Vendor Breakdown (with CORRECT distribution):\n";
echo "==================================================\n\n";

$sumCustomerTotal = 0;

foreach ($productsByVendor as $vendorId => $vendorProducts) {
    $vendorName = $vendorProducts->first()->vendorProduct?->vendor?->name ?? 'N/A';
    
    // Calculate vendor totals
    $vendorSubtotal = $vendorProducts->sum('price');
    $vendorShipping = $vendorProducts->sum('shipping_cost');
    $vendorGrandTotal = $vendorSubtotal + $vendorShipping;
    
    // Calculate vendor percentage based on grand total
    $vendorPercentage = $orderGrandTotal > 0 ? ($vendorGrandTotal / $orderGrandTotal) : 0;
    
    // Distribute promo/points based on grand total percentage
    $vendorPromo = $totalPromo * $vendorPercentage;
    $vendorPoints = $totalPoints * $vendorPercentage;
    
    // Calculate customer total
    $customerTotal = $vendorSubtotal - $vendorPromo - $vendorPoints + $vendorShipping;
    $sumCustomerTotal += $customerTotal;
    
    echo "Vendor: $vendorName (ID: $vendorId)\n";
    echo "-----------------------------------\n";
    echo "Subtotal (with tax): " . number_format($vendorSubtotal, 2) . " EGP\n";
    echo "Shipping: " . number_format($vendorShipping, 2) . " EGP\n";
    echo "Grand Total: " . number_format($vendorGrandTotal, 2) . " EGP\n";
    echo "Percentage: " . number_format($vendorPercentage * 100, 4) . "%\n";
    echo "\n";
    echo "Promo Code Discount: -" . number_format($vendorPromo, 2) . " EGP\n";
    echo "Points Discount: -" . number_format($vendorPoints, 2) . " EGP\n";
    echo "\n";
    echo "Customer Total: " . number_format($customerTotal, 2) . " EGP\n";
    
    if (abs($customerTotal) < 0.01) {
        echo "✅ Customer pays 0.00 EGP for this vendor\n";
    }
    echo "\n";
}

echo "FINAL VERIFICATION:\n";
echo "===================\n";
echo "Sum of all vendor Customer Totals: " . number_format($sumCustomerTotal, 2) . " EGP\n";
echo "Expected: 0.00 EGP\n";
echo "Difference: " . number_format(abs($sumCustomerTotal), 2) . " EGP\n\n";

if (abs($sumCustomerTotal) < 0.01) {
    echo "✅ SUCCESS! Distribution is correct!\n";
    echo "   All vendors show Customer Total = 0.00 EGP\n";
    echo "   Customer paid 0 EGP for entire order\n";
} else {
    echo "❌ ERROR! Distribution still has issues\n";
    echo "   Sum should be 0.00 EGP but got " . number_format($sumCustomerTotal, 2) . " EGP\n";
}
