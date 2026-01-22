<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "═══════════════════════════════════════════════════════════════\n";
echo "      Fix Old Accounting Entries for Refunds                   \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Get all refund accounting entries
$refundEntries = \Modules\Accounting\app\Models\AccountingEntry::where('type', 'refund')->get();

echo "Found {$refundEntries->count()} refund accounting entries\n\n";

foreach ($refundEntries as $entry) {
    $metadata = is_string($entry->metadata) ? json_decode($entry->metadata, true) : $entry->metadata;
    
    if (!$metadata || !isset($metadata['refund_request_id'])) {
        echo "Entry #{$entry->id}: Skipping (no refund_request_id in metadata)\n";
        continue;
    }
    
    $refundRequestId = $metadata['refund_request_id'];
    $refund = \Modules\Refund\app\Models\RefundRequest::find($refundRequestId);
    
    if (!$refund) {
        echo "Entry #{$entry->id}: Skipping (refund request #{$refundRequestId} not found)\n";
        continue;
    }
    
    // Calculate correct vendor deduction
    $correctVendorDeduction = $refund->total_products_amount 
        + $refund->total_shipping_amount 
        + ($refund->vendor_fees_amount ?? 0)
        - ($refund->vendor_discounts_amount ?? 0)
        - ($refund->return_shipping_cost ?? 0);
    
    $currentAmount = $entry->amount;
    
    echo "Entry #{$entry->id} (Refund #{$refund->id}):\n";
    echo "  Current Amount: " . number_format($currentAmount, 2) . " EGP\n";
    echo "  Correct Amount: " . number_format($correctVendorDeduction, 2) . " EGP\n";
    
    if (abs($currentAmount - $correctVendorDeduction) < 0.01) {
        echo "  ✅ Already correct, no update needed\n\n";
        continue;
    }
    
    echo "  ⚠️  MISMATCH detected!\n";
    echo "  Difference: " . number_format($currentAmount - $correctVendorDeduction, 2) . " EGP\n";
    
    // Calculate commission on the correct amount
    $commissionDetails = [];
    $totalCommission = 0;
    $totalCommissionRate = 0;
    $itemCount = 0;
    
    foreach ($refund->items as $item) {
        $orderProduct = $item->orderProduct;
        if ($orderProduct) {
            $commPercent = $orderProduct->commission ?? 0;
            $itemTotal = $item->total_price + $item->shipping_amount;
            $itemCommission = ($itemTotal * $commPercent) / 100;
            
            $totalCommission += $itemCommission;
            $totalCommissionRate += $commPercent;
            $itemCount++;
        }
    }
    
    $avgCommissionRate = $itemCount > 0 ? $totalCommissionRate / $itemCount : 0;
    $vendorAmount = $correctVendorDeduction - $totalCommission;
    
    echo "  New Commission: " . number_format($totalCommission, 2) . " EGP\n";
    echo "  New Vendor Amount: " . number_format($vendorAmount, 2) . " EGP\n";
    
    // Update the entry
    $entry->amount = $correctVendorDeduction;
    $entry->commission_amount = $totalCommission;
    $entry->commission_rate = $avgCommissionRate;
    $entry->vendor_amount = $vendorAmount;
    
    // Update metadata
    $metadata['vendor_deduction_amount'] = $correctVendorDeduction;
    $entry->metadata = $metadata;
    
    $entry->save();
    
    echo "  ✅ Updated successfully!\n\n";
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "                    Update Complete!                           \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Verify totals
$totalIncome = \Modules\Accounting\app\Models\AccountingEntry::where('type', 'income')->sum('amount');
$totalRefunds = \Modules\Accounting\app\Models\AccountingEntry::where('type', 'refund')->sum('amount');
$netIncome = $totalIncome - $totalRefunds;

echo "NEW TOTALS:\n";
echo "  Total Income: " . number_format($totalIncome, 2) . " EGP\n";
echo "  Total Refunds: " . number_format($totalRefunds, 2) . " EGP\n";
echo "  Net Income: " . number_format($netIncome, 2) . " EGP\n";
