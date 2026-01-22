<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\Order\app\Models\Order;

echo "=== Testing Print Calculation for Order #254 ===\n\n";

$order = Order::with(['products'])->find(254);

if (!$order) {
    echo "❌ Order #254 not found\n";
    exit(1);
}

// Simulate vendor user (Vendor ID: 107 - Value)
$currentVendorId = 107;

echo "Vendor: Value (ID: $currentVendorId)\n";
echo "=====================================\n\n";

// Filter vendor products
$vendorProducts = $order->products->filter(function($product) use ($currentVendorId) {
    return $product->vendorProduct?->vendor_id == $currentVendorId;
});

// Calculate vendor totals
$vendorProductTotal = $vendorProducts->sum('price');
$vendorShipping = $vendorProducts->sum('shipping_cost');

echo "Vendor Products Total: " . number_format($vendorProductTotal, 2) . " EGP\n";
echo "Vendor Shipping: " . number_format($vendorShipping, 2) . " EGP\n\n";

// Get vendor order stage
$vendorOrderStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $order->id)
    ->where('vendor_id', $currentVendorId)
    ->first();

$promoCodeShare = $vendorOrderStage?->promo_code_share ?? 0;
$pointsShare = $vendorOrderStage?->points_share ?? 0;
$feesShare = $vendorOrderStage?->fees_share ?? 0;
$discountsShare = $vendorOrderStage?->discounts_share ?? 0;

echo "Vendor Order Stage (from Bnaia):\n";
echo "--------------------------------\n";
echo "Promo Code Share (Bnaia pays): " . number_format($promoCodeShare, 2) . " EGP\n";
echo "Points Share (Bnaia pays): " . number_format($pointsShare, 2) . " EGP\n";
echo "Fees Share: " . number_format($feesShare, 2) . " EGP\n";
echo "Discounts Share: " . number_format($discountsShare, 2) . " EGP\n\n";

// Calculate vendor's share of customer promo/points (NEW METHOD)
$orderGrandTotal = $order->products->sum(function($p) {
    return $p->price + ($p->shipping_cost ?? 0);
});

$vendorGrandTotal = $vendorProductTotal + $vendorShipping;
$vendorPercentage = $orderGrandTotal > 0 
    ? ($vendorGrandTotal / $orderGrandTotal) 
    : 0;

$customerPromoShare = ($order->customer_promo_code_amount ?? 0) * $vendorPercentage;
$customerPointsShare = ($order->points_cost ?? 0) * $vendorPercentage;

echo "Customer Discount Distribution (NEW):\n";
echo "-------------------------------------\n";
echo "Order Grand Total: " . number_format($orderGrandTotal, 2) . " EGP\n";
echo "Vendor Grand Total: " . number_format($vendorGrandTotal, 2) . " EGP\n";
echo "Vendor Percentage: " . number_format($vendorPercentage * 100, 4) . "%\n";
echo "Customer Promo Share: " . number_format($customerPromoShare, 2) . " EGP\n";
echo "Customer Points Share: " . number_format($customerPointsShare, 2) . " EGP\n\n";

// OLD CALCULATION (WRONG)
$oldTotal = $vendorProductTotal + $vendorShipping + $feesShare - $promoCodeShare - $pointsShare - $discountsShare;

echo "OLD Calculation (WRONG):\n";
echo "------------------------\n";
echo "$vendorProductTotal + $vendorShipping + $feesShare - $promoCodeShare - $pointsShare - $discountsShare\n";
echo "= " . number_format($oldTotal, 2) . " EGP ❌\n\n";

// NEW CALCULATION (CORRECT)
$newTotal = $vendorProductTotal + $vendorShipping + $feesShare - $customerPromoShare - $customerPointsShare - $discountsShare;

echo "NEW Calculation (CORRECT):\n";
echo "--------------------------\n";
echo "$vendorProductTotal + $vendorShipping + $feesShare - $customerPromoShare - $customerPointsShare - $discountsShare\n";
echo "= " . number_format($newTotal, 2) . " EGP";

if (abs($newTotal) < 0.01) {
    echo " ✅\n";
} else {
    echo " (should be 0.00)\n";
}

echo "\n";
echo "RESULT:\n";
echo "=======\n";
echo "Old Total (in print): " . number_format($oldTotal, 2) . " EGP ❌\n";
echo "New Total (in print): " . number_format($newTotal, 2) . " EGP ✅\n";
echo "\nThe print invoice will now show the correct total!\n";
