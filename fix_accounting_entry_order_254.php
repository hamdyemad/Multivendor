<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\Accounting\app\Models\AccountingEntry;
use Modules\Order\app\Models\Order;

echo "=== Fixing Accounting Entry for Order #254 ===\n\n";

$order = Order::with(['products'])->find(254);

if (!$order) {
    echo "❌ Order #254 not found\n";
    exit(1);
}

// Find accounting entry for Vendor Value (ID: 107)
$vendorId = 107;
$entry = AccountingEntry::where('order_id', 254)
    ->where('vendor_id', $vendorId)
    ->where('type', 'income')
    ->first();

if (!$entry) {
    echo "❌ Accounting entry not found for Order #254, Vendor #107\n";
    exit(1);
}

echo "Current Accounting Entry:\n";
echo "========================\n";
echo "Total Amount: " . number_format($entry->amount, 2) . " EGP\n";
echo "Commission Amount: " . number_format($entry->commission_amount, 2) . " EGP\n";
echo "Vendor Amount: " . number_format($entry->vendor_amount, 2) . " EGP\n\n";

// Calculate correct values
$vendorProducts = $order->products->where('vendor_id', $vendorId);
$vendorTotal = $vendorProducts->sum('price');
$vendorShipping = $vendorProducts->sum('shipping_cost');

// Calculate vendor's share of customer promo/points
$orderGrandTotal = $order->products->sum(function($p) {
    return $p->price + ($p->shipping_cost ?? 0);
});

$vendorGrandTotal = $vendorTotal + $vendorShipping;
$vendorPercentage = $orderGrandTotal > 0 ? ($vendorGrandTotal / $orderGrandTotal) : 0;

$customerPromoShare = ($order->customer_promo_code_amount ?? 0) * $vendorPercentage;
$customerPointsShare = ($order->points_cost ?? 0) * $vendorPercentage;

// Get fees and discounts
$feesTotal = \Modules\Order\app\Models\OrderExtraFeeDiscount::where('order_id', 254)
    ->where('vendor_id', $vendorId)
    ->where('type', 'fee')
    ->sum('cost') ?? 0;

$discountsTotal = \Modules\Order\app\Models\OrderExtraFeeDiscount::where('order_id', 254)
    ->where('vendor_id', $vendorId)
    ->where('type', 'discount')
    ->sum('cost') ?? 0;

// Calculate correct total
$correctTotal = $vendorTotal + $vendorShipping + $feesTotal - $discountsTotal;

// Commission stays the same
$commissionAmount = $entry->commission_amount;

// Calculate correct vendor amount
$correctVendorAmount = $correctTotal - $commissionAmount;

echo "Correct Values:\n";
echo "===============\n";
echo "Vendor Products: " . number_format($vendorTotal, 2) . " EGP\n";
echo "Vendor Shipping: " . number_format($vendorShipping, 2) . " EGP\n";
echo "Fees: " . number_format($feesTotal, 2) . " EGP\n";
echo "Discounts: " . number_format($discountsTotal, 2) . " EGP\n";
echo "Customer Promo Share (metadata only): " . number_format($customerPromoShare, 2) . " EGP\n";
echo "Customer Points Share (metadata only): " . number_format($customerPointsShare, 2) . " EGP\n";
echo "\n";
echo "Total Amount (what vendor receives from Bnaia): " . number_format($correctTotal, 2) . " EGP\n";
echo "Commission Amount: " . number_format($commissionAmount, 2) . " EGP\n";
echo "Vendor Amount: " . number_format($correctVendorAmount, 2) . " EGP\n\n";

// Update the entry
$entry->amount = $correctTotal;
$entry->vendor_amount = $correctVendorAmount;

// Update metadata to include customer promo/points shares
$metadata = $entry->metadata ?? [];
$metadata['customer_promo_share'] = $customerPromoShare;
$metadata['customer_points_share'] = $customerPointsShare;
$entry->metadata = $metadata;

$entry->save();

echo "✅ Accounting entry updated successfully!\n";
echo "\nUpdated Entry:\n";
echo "==============\n";
echo "Total Amount: " . number_format($entry->amount, 2) . " EGP\n";
echo "Commission Amount: " . number_format($entry->commission_amount, 2) . " EGP\n";
echo "Vendor Amount: " . number_format($entry->vendor_amount, 2) . " EGP\n";
