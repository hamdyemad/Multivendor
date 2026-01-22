<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "═══════════════════════════════════════════════════════════════\n";
echo "           Dashboard Totals Verification                       \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Get all accounting entries
$incomeEntries = \Modules\Accounting\app\Models\AccountingEntry::where('type', 'income')->get();
$refundEntries = \Modules\Accounting\app\Models\AccountingEntry::where('type', 'refund')->get();

echo "INCOME ENTRIES:\n";
echo str_repeat("─", 63) . "\n";

$totalIncome = 0;
foreach ($incomeEntries as $entry) {
    $vendor = \Modules\Vendor\app\Models\Vendor::find($entry->vendor_id);
    $vendorName = $vendor ? $vendor->name : "N/A";
    
    echo "Entry #{$entry->id}:\n";
    echo "  Vendor: {$vendorName} (#{$entry->vendor_id})\n";
    echo "  Amount: " . number_format($entry->amount, 2) . " EGP\n";
    echo "  Commission: " . number_format($entry->commission_amount, 2) . " EGP\n";
    echo "  Vendor Amount: " . number_format($entry->vendor_amount, 2) . " EGP\n";
    echo "  Created: {$entry->created_at}\n\n";
    
    $totalIncome += $entry->amount;
}

echo "Total Income: " . number_format($totalIncome, 2) . " EGP\n\n";

echo "REFUND ENTRIES:\n";
echo str_repeat("─", 63) . "\n";

$totalRefunds = 0;
foreach ($refundEntries as $entry) {
    $vendor = \Modules\Vendor\app\Models\Vendor::find($entry->vendor_id);
    $vendorName = $vendor ? $vendor->name : "N/A";
    
    echo "Entry #{$entry->id}:\n";
    echo "  Vendor: {$vendorName} (#{$entry->vendor_id})\n";
    echo "  Amount: " . number_format($entry->amount, 2) . " EGP\n";
    echo "  Commission: " . number_format($entry->commission_amount, 2) . " EGP\n";
    echo "  Vendor Amount: " . number_format($entry->vendor_amount, 2) . " EGP\n";
    
    // Get refund details
    $metadata = is_string($entry->metadata) ? json_decode($entry->metadata, true) : $entry->metadata;
    if ($metadata) {
        echo "  Refund Details:\n";
        echo "    Customer Refund: " . ($metadata['customer_refund_amount'] ?? 'N/A') . " EGP\n";
        echo "    Vendor Deduction: " . ($metadata['vendor_deduction_amount'] ?? 'N/A') . " EGP\n";
        echo "    Products: " . ($metadata['products_amount'] ?? 'N/A') . " EGP\n";
        echo "    Shipping: " . ($metadata['shipping_amount'] ?? 'N/A') . " EGP\n";
        echo "    Fees: " . ($metadata['vendor_fees_amount'] ?? 'N/A') . " EGP\n";
        echo "    Discounts: " . ($metadata['vendor_discounts_amount'] ?? 'N/A') . " EGP\n";
    }
    echo "  Created: {$entry->created_at}\n\n";
    
    $totalRefunds += $entry->amount;
}

echo "Total Refunds: " . number_format($totalRefunds, 2) . " EGP\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "SUMMARY:\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "Total Income: " . number_format($totalIncome, 2) . " EGP\n";
echo "Total Refunds: " . number_format($totalRefunds, 2) . " EGP\n";
echo "Net Income: " . number_format($totalIncome - $totalRefunds, 2) . " EGP\n\n";

// Check if the refund entries have correct amounts
echo "VERIFICATION:\n";
echo str_repeat("─", 63) . "\n";

// Get refund requests
$refundRequests = \Modules\Refund\app\Models\RefundRequest::where('status', 'refunded')->get();

echo "Refund Requests (Status: refunded):\n\n";

foreach ($refundRequests as $refund) {
    echo "Refund #{$refund->id} ({$refund->refund_number}):\n";
    echo "  Vendor: {$refund->vendor_id}\n";
    echo "  Customer Refund Amount: {$refund->total_refund_amount} EGP\n";
    
    // Calculate vendor deduction
    $vendorDeduction = $refund->total_products_amount 
        + $refund->total_shipping_amount 
        + ($refund->vendor_fees_amount ?? 0)
        - ($refund->vendor_discounts_amount ?? 0)
        - ($refund->return_shipping_cost ?? 0);
    
    echo "  Calculated Vendor Deduction:\n";
    echo "    Products: {$refund->total_products_amount} EGP\n";
    echo "    + Shipping: {$refund->total_shipping_amount} EGP\n";
    echo "    + Fees: " . ($refund->vendor_fees_amount ?? 0) . " EGP\n";
    echo "    - Discounts: " . ($refund->vendor_discounts_amount ?? 0) . " EGP\n";
    echo "    - Return Shipping: " . ($refund->return_shipping_cost ?? 0) . " EGP\n";
    echo "    = " . number_format($vendorDeduction, 2) . " EGP\n";
    
    // Find corresponding accounting entry
    $accountingEntry = \Modules\Accounting\app\Models\AccountingEntry::where('type', 'refund')
        ->where('vendor_id', $refund->vendor_id)
        ->where('order_id', $refund->order_id)
        ->whereJsonContains('metadata->refund_request_id', $refund->id)
        ->first();
    
    if ($accountingEntry) {
        echo "  Accounting Entry Amount: {$accountingEntry->amount} EGP\n";
        
        if (abs($accountingEntry->amount - $vendorDeduction) < 0.01) {
            echo "  ✅ MATCH: Accounting entry matches calculated vendor deduction\n";
        } else {
            echo "  ❌ MISMATCH: Accounting entry does NOT match!\n";
            echo "     Expected: " . number_format($vendorDeduction, 2) . " EGP\n";
            echo "     Got: " . number_format($accountingEntry->amount, 2) . " EGP\n";
            echo "     Difference: " . number_format($accountingEntry->amount - $vendorDeduction, 2) . " EGP\n";
        }
    } else {
        echo "  ⚠️  WARNING: No accounting entry found for this refund\n";
    }
    
    echo "\n";
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "                    Verification Complete!                     \n";
echo "═══════════════════════════════════════════════════════════════\n";
