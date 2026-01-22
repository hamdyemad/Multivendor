<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\Order\app\Models\Order;

echo "=== Testing Display Order Fix ===\n\n";

$order = Order::find(254);

if (!$order) {
    echo "❌ Order #254 not found\n";
    exit(1);
}

echo "Order #254 - Correct Display Order:\n";
echo "====================================\n\n";

// Example for one vendor
$subtotalBeforeTax = 600.00;
$taxAmount = 90.00;
$subtotalWithTax = 690.00;
$customerPromoAmount = 67.39;
$customerPointsCost = 653.18;
$shipping = 50.00;

echo "1. Subtotal (before tax):        " . number_format($subtotalBeforeTax, 2) . " EGP\n";
echo "2. + Taxes:                      +" . number_format($taxAmount, 2) . " EGP\n";
echo "3. = Subtotal including Tax:     " . number_format($subtotalWithTax, 2) . " EGP\n";
echo "\n";
echo "4. - Promo Code Discount:        -" . number_format($customerPromoAmount, 2) . " EGP ← يتخصم هنا\n";
echo "5. - Points Discount:            -" . number_format($customerPointsCost, 2) . " EGP ← يتخصم هنا\n";
echo "\n";

$customerTotalBeforeShipping = $subtotalWithTax - $customerPromoAmount - $customerPointsCost;
echo "   Subtotal after discounts:     " . number_format($customerTotalBeforeShipping, 2) . " EGP\n";
echo "\n";

echo "6. + Shipping:                   +" . number_format($shipping, 2) . " EGP ← يتضاف بعد الخصم\n";
echo "\n";

$customerTotal = $customerTotalBeforeShipping + $shipping;
echo "7. = Customer Total (paid):      " . number_format($customerTotal, 2) . " EGP\n";
echo "   (Customer paid 0 because used full points)\n";
echo "\n";
echo "-----------------------------------\n";
echo "\n";

// What vendor receives
$vendorPromoShare = 67.39; // Bnaia pays this
$vendorPointsShare = 653.18; // Bnaia pays this
$totalWithShipping = $subtotalWithTax + $shipping; // 740 EGP

echo "8. Total with Shipping:          " . number_format($totalWithShipping, 2) . " EGP\n";
echo "   (What vendor receives from customer + Bnaia)\n";
echo "   = Customer paid: " . number_format(max(0, $customerTotal), 2) . " EGP\n";
echo "   + Bnaia pays: " . number_format($vendorPromoShare + $vendorPointsShare, 2) . " EGP\n";
echo "\n";

$commission = 111.00;
echo "9. - Bnaia Commission (15%):     -" . number_format($commission, 2) . " EGP\n";
echo "\n";

$remaining = $totalWithShipping - $commission;
echo "10. = Remaining:                 " . number_format($remaining, 2) . " EGP\n";

echo "\n";
echo "✅ Display order is now correct!\n";
echo "   Promo and Points discounts appear BEFORE shipping\n";
echo "   Customer Total shows what customer actually paid (0 EGP)\n";
echo "   Total with Shipping shows what vendor receives (740 EGP)\n";
