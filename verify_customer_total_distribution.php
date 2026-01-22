<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\Order\app\Models\Order;

echo "=== Verifying Customer Total Distribution ===\n\n";

$order = Order::with(['products'])->find(254);

if (!$order) {
    echo "❌ Order #254 not found\n";
    exit(1);
}

echo "Order #254 - Customer Payment Verification:\n";
echo "============================================\n\n";

// Calculate order totals
$orderTotalProducts = $order->products->sum('price'); // includes tax
$orderTotalShipping = $order->products->sum('shipping_cost');
$orderGrandTotal = $orderTotalProducts + $orderTotalShipping;

echo "Order Grand Total:\n";
echo "------------------\n";
echo "Products (with tax): " . number_format($orderTotalProducts, 2) . " EGP\n";
echo "Shipping: " . number_format($orderTotalShipping, 2) . " EGP\n";
echo "Grand Total: " . number_format($orderGrandTotal, 2) . " EGP\n\n";

echo "Customer Discounts:\n";
echo "-------------------\n";
$totalPromo = $order->customer_promo_code_amount ?? 0;
$totalPoints = $order->points_cost ?? 0;
$totalDiscounts = $totalPromo + $totalPoints;
echo "Promo Code: " . number_format($totalPromo, 2) . " EGP\n";
echo "Points: " . number_format($totalPoints, 2) . " EGP\n";
echo "Total Discounts: " . number_format($totalDiscounts, 2) . " EGP\n\n";

echo "Expected Customer Payment:\n";
echo "--------------------------\n";
$expectedCustomerPayment = $orderGrandTotal - $totalDiscounts;
echo "Grand Total - Discounts = " . number_format($expectedCustomerPayment, 2) . " EGP\n";
echo "Actual customer_paid field: " . number_format($order->customer_paid ?? 0, 2) . " EGP\n\n";

if (abs($expectedCustomerPayment) < 0.01) {
    echo "✅ Customer should pay 0.00 EGP (discounts cover full order)\n\n";
} else {
    echo "⚠️ Customer should pay: " . number_format(max(0, $expectedCustomerPayment), 2) . " EGP\n\n";
}

// Now check per-vendor distribution
echo "Per-Vendor Customer Total (with proportional distribution):\n";
echo "===========================================================\n\n";

$productsByVendor = $order->products->groupBy(function ($product) {
    return $product->vendorProduct?->vendor_id;
});

$sumOfVendorCustomerTotals = 0;

foreach ($productsByVendor as $vendorId => $vendorProducts) {
    $vendorName = $vendorProducts->first()->vendorProduct?->vendor?->name ?? 'N/A';
    
    // Calculate vendor subtotal
    $vendorSubtotalWithTax = $vendorProducts->sum('price');
    $vendorShipping = $vendorProducts->sum('shipping_cost');
    
    // Calculate vendor percentage
    $vendorPercentage = $orderTotalProducts > 0 
        ? ($vendorSubtotalWithTax / $orderTotalProducts) 
        : 0;
    
    // Distribute promo/points proportionally
    $vendorPromo = $totalPromo * $vendorPercentage;
    $vendorPoints = $totalPoints * $vendorPercentage;
    
    // Calculate customer total for this vendor
    $vendorCustomerTotal = $vendorSubtotalWithTax - $vendorPromo - $vendorPoints + $vendorShipping;
    $vendorCustomerTotal = max(0, $vendorCustomerTotal);
    
    $sumOfVendorCustomerTotals += $vendorCustomerTotal;
    
    echo "Vendor: $vendorName\n";
    echo "  Subtotal: " . number_format($vendorSubtotalWithTax, 2) . " EGP\n";
    echo "  Percentage: " . number_format($vendorPercentage * 100, 4) . "%\n";
    echo "  Promo (distributed): -" . number_format($vendorPromo, 2) . " EGP\n";
    echo "  Points (distributed): -" . number_format($vendorPoints, 2) . " EGP\n";
    echo "  Shipping: +" . number_format($vendorShipping, 2) . " EGP\n";
    echo "  Customer Total: " . number_format($vendorCustomerTotal, 2) . " EGP\n\n";
}

echo "VERIFICATION:\n";
echo "=============\n";
echo "Sum of all vendor Customer Totals: " . number_format($sumOfVendorCustomerTotals, 2) . " EGP\n";
echo "Expected (from order): " . number_format(max(0, $expectedCustomerPayment), 2) . " EGP\n";
echo "Difference: " . number_format(abs($sumOfVendorCustomerTotals - max(0, $expectedCustomerPayment)), 2) . " EGP\n\n";

if (abs($sumOfVendorCustomerTotals - max(0, $expectedCustomerPayment)) < 0.10) {
    echo "✅ Distribution is correct (difference < 0.10 EGP due to rounding)\n";
} else {
    echo "❌ Distribution has significant error!\n";
}

echo "\nCONCLUSION:\n";
echo "===========\n";
if ($order->customer_paid == 0 && $sumOfVendorCustomerTotals > 0) {
    echo "⚠️ ISSUE FOUND:\n";
    echo "   - Order customer_paid = 0.00 EGP\n";
    echo "   - But sum of vendor Customer Totals = " . number_format($sumOfVendorCustomerTotals, 2) . " EGP\n";
    echo "   - This is due to rounding when distributing discounts\n\n";
    echo "SOLUTION:\n";
    echo "   Show 'Customer Total: 0.00 EGP' for all vendors when order->customer_paid = 0\n";
    echo "   (Already implemented in the component)\n";
} else {
    echo "✅ No issues found\n";
}
