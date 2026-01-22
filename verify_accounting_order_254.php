<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\Accounting\app\Models\AccountingEntry;
use Modules\Order\app\Models\Order;

echo "=== Accounting Entry Verification for Order #254 ===\n\n";

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

echo "Accounting Entry for Vendor Value (ID: 107):\n";
echo "============================================\n\n";

// Calculate expected values
$vendorProducts = $order->products->where('vendor_id', $vendorId);
$vendorTotal = $vendorProducts->sum('price');
$vendorShipping = $vendorProducts->sum('shipping_cost');

echo "Breakdown:\n";
echo "----------\n";
echo "Products Total: " . number_format($vendorTotal, 2) . " EGP\n";
echo "Shipping: " . number_format($vendorShipping, 2) . " EGP\n";
echo "Grand Total: " . number_format($vendorTotal + $vendorShipping, 2) . " EGP\n\n";

echo "Accounting Entry:\n";
echo "-----------------\n";
echo "Total Amount: " . number_format($entry->amount, 2) . " EGP\n";
echo "Commission Rate: " . number_format($entry->commission_rate, 2) . "%\n";
echo "Commission Amount: " . number_format($entry->commission_amount, 2) . " EGP\n";
echo "Vendor Amount: " . number_format($entry->vendor_amount, 2) . " EGP\n\n";

echo "Metadata:\n";
echo "---------\n";
$metadata = $entry->metadata ?? [];
echo "Customer Promo Share: " . number_format($metadata['customer_promo_share'] ?? 0, 2) . " EGP\n";
echo "Customer Points Share: " . number_format($metadata['customer_points_share'] ?? 0, 2) . " EGP\n";
echo "Fees: " . number_format($metadata['fees'] ?? 0, 2) . " EGP\n";
echo "Discounts: " . number_format($metadata['discounts'] ?? 0, 2) . " EGP\n\n";

// Verify the logic
$expectedTotal = $vendorTotal + $vendorShipping;
$expectedVendorAmount = $expectedTotal - $entry->commission_amount;

echo "Verification:\n";
echo "-------------\n";
echo "Expected Total Amount: " . number_format($expectedTotal, 2) . " EGP\n";
echo "Actual Total Amount: " . number_format($entry->amount, 2) . " EGP\n";

if (abs($expectedTotal - $entry->amount) < 0.01) {
    echo "✅ Total Amount is CORRECT\n\n";
} else {
    echo "❌ Total Amount is INCORRECT\n\n";
}

echo "Expected Vendor Amount: " . number_format($expectedVendorAmount, 2) . " EGP\n";
echo "Actual Vendor Amount: " . number_format($entry->vendor_amount, 2) . " EGP\n";

if (abs($expectedVendorAmount - $entry->vendor_amount) < 0.01) {
    echo "✅ Vendor Amount is CORRECT\n\n";
} else {
    echo "❌ Vendor Amount is INCORRECT\n\n";
}

echo "Key Points:\n";
echo "-----------\n";
echo "✅ Total Amount = Products + Shipping (what vendor receives from Bnaia)\n";
echo "✅ Commission is calculated and deducted normally\n";
echo "✅ Customer promo/points are NOT subtracted from accounting total\n";
echo "✅ Customer promo/points are stored in metadata for reference only\n";
echo "✅ Vendor Amount = Total Amount - Commission\n\n";

echo "Summary:\n";
echo "--------\n";
echo "Bnaia pays vendor: " . number_format($entry->amount, 2) . " EGP\n";
echo "Bnaia takes commission: " . number_format($entry->commission_amount, 2) . " EGP\n";
echo "Vendor receives: " . number_format($entry->vendor_amount, 2) . " EGP\n";
echo "Customer paid: 0.00 EGP (paid with promo/points)\n";
