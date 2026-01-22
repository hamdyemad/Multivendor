<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\Order\app\Models\Order;

echo "=== Checking Order #254 Customer Payment ===\n\n";

$order = Order::with(['products'])->find(254);

if (!$order) {
    echo "❌ Order #254 not found\n";
    exit(1);
}

echo "Order #254 Financial Details:\n";
echo "==============================\n\n";

// Calculate order totals
$totalProducts = $order->products->sum('price'); // includes tax
$totalShipping = $order->products->sum('shipping_cost');
$orderTotal = $totalProducts + $totalShipping;

echo "Total Products (with tax): " . number_format($totalProducts, 2) . " EGP\n";
echo "Total Shipping: " . number_format($totalShipping, 2) . " EGP\n";
echo "Order Total: " . number_format($orderTotal, 2) . " EGP\n\n";

echo "Customer Discounts:\n";
echo "-------------------\n";
echo "Promo Code Amount: " . number_format($order->customer_promo_code_amount ?? 0, 2) . " EGP\n";
echo "Points Cost: " . number_format($order->points_cost ?? 0, 2) . " EGP\n";
echo "Total Discounts: " . number_format(($order->customer_promo_code_amount ?? 0) + ($order->points_cost ?? 0), 2) . " EGP\n\n";

echo "Customer Payment Calculation:\n";
echo "-----------------------------\n";
$customerShouldPay = $orderTotal - ($order->customer_promo_code_amount ?? 0) - ($order->points_cost ?? 0);
echo "Order Total: " . number_format($orderTotal, 2) . " EGP\n";
echo "- Promo Code: -" . number_format($order->customer_promo_code_amount ?? 0, 2) . " EGP\n";
echo "- Points: -" . number_format($order->points_cost ?? 0, 2) . " EGP\n";
echo "= Customer Should Pay: " . number_format($customerShouldPay, 2) . " EGP\n\n";

if ($customerShouldPay < 0) {
    echo "⚠️ Customer Should Pay is NEGATIVE!\n";
    echo "   This means customer used more points than order total.\n";
    echo "   Customer Total should be: 0.00 EGP\n\n";
} else {
    echo "✅ Customer Should Pay: " . number_format($customerShouldPay, 2) . " EGP\n\n";
}

// Check what's stored in database
echo "Database Values:\n";
echo "----------------\n";
echo "order->total: " . number_format($order->total ?? 0, 2) . " EGP\n";
echo "order->customer_paid: " . number_format($order->customer_paid ?? 0, 2) . " EGP\n";
echo "order->points_used: " . number_format($order->points_used ?? 0, 2) . " points\n";
echo "order->points_cost: " . number_format($order->points_cost ?? 0, 2) . " EGP\n\n";

// The issue
echo "THE ISSUE:\n";
echo "==========\n";
echo "When we distribute promo/points proportionally to each vendor,\n";
echo "the sum of all vendor 'Customer Totals' might not equal 0 due to rounding.\n\n";
echo "SOLUTION:\n";
echo "=========\n";
echo "Instead of showing 'Customer Total' per vendor, we should:\n";
echo "1. Show the discounts (promo/points) per vendor\n";
echo "2. Show 'Total with Shipping' (what vendor receives)\n";
echo "3. NOT show 'Customer Total' per vendor (it's confusing)\n";
echo "4. OR show a note: 'Customer paid 0 EGP for entire order'\n";
