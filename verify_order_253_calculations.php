<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "═══════════════════════════════════════════════════════════════\n";
echo "           Order #253 - Complete Verification Report           \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$order = \Modules\Order\app\Models\Order::with(['products.vendorProduct.product', 'products.taxes'])->find(253);

if (!$order) {
    echo "Order #253 not found!\n";
    exit;
}

echo "Order Number: {$order->order_number}\n";
echo "Order Total: {$order->total_price} EGP\n";
echo "Customer Paid: {$order->customer_paid} EGP\n\n";

// Get all vendors in this order
$vendors = $order->products->groupBy('vendor_id');

foreach ($vendors as $vendorId => $products) {
    $vendor = \Modules\Vendor\app\Models\Vendor::find($vendorId);
    
    echo "┌─────────────────────────────────────────────────────────────┐\n";
    echo "│ VENDOR #{$vendorId}: " . str_pad($vendor->name, 47) . "│\n";
    echo "└─────────────────────────────────────────────────────────────┘\n\n";
    
    // Calculate vendor totals
    $vendorProductsTotal = 0;
    $vendorShipping = 0;
    $vendorTax = 0;
    $vendorCommissionAmount = 0;
    
    echo "Products:\n";
    echo str_repeat("─", 63) . "\n";
    
    foreach ($products as $product) {
        $productTotal = $product->price;
        $productTax = $product->taxes->sum('amount') ?? 0;
        $productBeforeTax = $productTotal - $productTax;
        $shipping = $product->shipping_cost ?? 0;
        $commission = $product->commission ?? 0;
        
        echo "  Product #{$product->id}: {$product->vendorProduct->product->name}\n";
        echo "    Quantity: {$product->quantity}\n";
        echo "    Price (with tax): {$productTotal} EGP\n";
        echo "    Tax: {$productTax} EGP\n";
        echo "    Price (before tax): {$productBeforeTax} EGP\n";
        echo "    Shipping: {$shipping} EGP\n";
        echo "    Commission: {$commission}%\n";
        
        $productWithShipping = $productTotal + $shipping;
        $productCommission = ($productWithShipping * $commission) / 100;
        
        echo "    Total (product + shipping): {$productWithShipping} EGP\n";
        echo "    Commission Amount: " . number_format($productCommission, 2) . " EGP\n\n";
        
        $vendorProductsTotal += $productTotal;
        $vendorShipping += $shipping;
        $vendorTax += $productTax;
        $vendorCommissionAmount += $productCommission;
    }
    
    // Get fees and discounts
    $vendorFees = \Modules\Order\app\Models\OrderExtraFeeDiscount::where('order_id', $order->id)
        ->where('vendor_id', $vendorId)
        ->where('type', 'fee')
        ->sum('cost') ?? 0;
    
    $vendorDiscounts = \Modules\Order\app\Models\OrderExtraFeeDiscount::where('order_id', $order->id)
        ->where('vendor_id', $vendorId)
        ->where('type', 'discount')
        ->sum('cost') ?? 0;
    
    // Get promo and points shares
    $vendorShares = \Illuminate\Support\Facades\DB::table('vendor_order_stages')
        ->where('order_id', $order->id)
        ->where('vendor_id', $vendorId)
        ->first();
    
    $promoShare = $vendorShares ? ($vendorShares->promo_code_share ?? 0) : 0;
    $pointsShare = $vendorShares ? ($vendorShares->points_share ?? 0) : 0;
    
    echo "Vendor Totals:\n";
    echo str_repeat("─", 63) . "\n";
    echo "  Products Total (with tax): " . number_format($vendorProductsTotal, 2) . " EGP\n";
    echo "  Shipping Total: " . number_format($vendorShipping, 2) . " EGP\n";
    echo "  Fees: " . number_format($vendorFees, 2) . " EGP\n";
    echo "  Discounts: -" . number_format($vendorDiscounts, 2) . " EGP\n";
    echo "  Promo Code Share (Bnaia pays): " . number_format($promoShare, 2) . " EGP\n";
    echo "  Points Share (Bnaia pays): " . number_format($pointsShare, 2) . " EGP\n";
    
    $vendorTotal = $vendorProductsTotal + $vendorShipping + $vendorFees - $vendorDiscounts;
    
    echo "\n  Total with Shipping + Fees - Discounts: " . number_format($vendorTotal, 2) . " EGP\n";
    echo "  Commission: " . number_format($vendorCommissionAmount, 2) . " EGP\n";
    
    $remainingBeforeRefund = $vendorTotal - $vendorCommissionAmount;
    echo "  = Remaining Before Refund: " . number_format($remainingBeforeRefund, 2) . " EGP\n\n";
    
    // Get refunds
    $refunds = \Modules\Refund\app\Models\RefundRequest::where('order_id', $order->id)
        ->where('vendor_id', $vendorId)
        ->where('status', 'refunded')
        ->with('items.orderProduct')
        ->get();
    
    if ($refunds->count() > 0) {
        echo "Refunds:\n";
        echo str_repeat("─", 63) . "\n";
        
        $totalCustomerRefund = 0;
        $totalVendorDeduction = 0;
        $totalRefundedCommission = 0;
        
        foreach ($refunds as $refund) {
            echo "  Refund #{$refund->id} ({$refund->refund_number}) - Status: {$refund->status}\n";
            echo "  ─────────────────────────────────────────────────────────\n";
            
            // Customer refund amount
            echo "    Customer Refund Amount: {$refund->total_refund_amount} EGP\n";
            echo "      (What customer gets back)\n\n";
            
            // Vendor deduction calculation
            $vendorDeduction = $refund->total_products_amount 
                + $refund->total_shipping_amount 
                + ($refund->vendor_fees_amount ?? 0)
                - ($refund->vendor_discounts_amount ?? 0)
                - ($refund->return_shipping_cost ?? 0);
            
            echo "    Vendor Deduction Calculation:\n";
            echo "      Products: {$refund->total_products_amount} EGP\n";
            echo "      + Shipping: {$refund->total_shipping_amount} EGP\n";
            echo "      + Fees: " . ($refund->vendor_fees_amount ?? 0) . " EGP\n";
            echo "      - Discounts: " . ($refund->vendor_discounts_amount ?? 0) . " EGP\n";
            echo "      - Return Shipping: " . ($refund->return_shipping_cost ?? 0) . " EGP\n";
            echo "      = Vendor Deduction: " . number_format($vendorDeduction, 2) . " EGP\n";
            echo "      (What gets deducted from vendor balance)\n\n";
            
            // Refunded commission
            $refundCommission = 0;
            echo "    Refunded Commission Calculation:\n";
            foreach ($refund->items as $item) {
                $orderProduct = $item->orderProduct;
                if ($orderProduct) {
                    $commPercent = $orderProduct->commission ?? 0;
                    $itemTotal = $item->total_price + $item->shipping_amount;
                    $itemCommission = ($itemTotal * $commPercent) / 100;
                    $refundCommission += $itemCommission;
                    
                    echo "      Product #{$orderProduct->id} (Qty {$item->quantity}):\n";
                    echo "        Amount: " . number_format($itemTotal, 2) . " EGP\n";
                    echo "        Commission {$commPercent}%: " . number_format($itemCommission, 2) . " EGP\n";
                }
            }
            echo "      = Total Refunded Commission: " . number_format($refundCommission, 2) . " EGP\n";
            echo "      (Commission returned to vendor)\n\n";
            
            $totalCustomerRefund += $refund->total_refund_amount;
            $totalVendorDeduction += $vendorDeduction;
            $totalRefundedCommission += $refundCommission;
        }
        
        echo "  Total Summary:\n";
        echo "  ─────────────────────────────────────────────────────────\n";
        echo "    Total Customer Refund: " . number_format($totalCustomerRefund, 2) . " EGP\n";
        echo "    Total Vendor Deduction: " . number_format($totalVendorDeduction, 2) . " EGP\n";
        echo "    Total Refunded Commission: " . number_format($totalRefundedCommission, 2) . " EGP\n\n";
        
        // Final remaining calculation
        echo "Final Remaining Calculation:\n";
        echo str_repeat("─", 63) . "\n";
        echo "  Remaining Before Refund: " . number_format($remainingBeforeRefund, 2) . " EGP\n";
        echo "  - Vendor Deduction: " . number_format($totalVendorDeduction, 2) . " EGP\n";
        echo "  + Refunded Commission: " . number_format($totalRefundedCommission, 2) . " EGP\n";
        
        $netRefundImpact = $totalVendorDeduction - $totalRefundedCommission;
        echo "  = Net Refund Impact: " . number_format($netRefundImpact, 2) . " EGP\n\n";
        
        $finalRemaining = $remainingBeforeRefund - $netRefundImpact;
        echo "  ╔═══════════════════════════════════════════════════════╗\n";
        echo "  ║  FINAL REMAINING: " . str_pad(number_format($finalRemaining, 2) . " EGP", 35) . "║\n";
        echo "  ╚═══════════════════════════════════════════════════════╝\n";
        
        // Verification
        if (abs($finalRemaining) < 0.01) {
            echo "\n  ✅ CORRECT: All products refunded, remaining = 0\n";
        } else {
            // Calculate what should remain
            $originalTotal = $vendorProductsTotal + $vendorShipping;
            $percentRemaining = ($finalRemaining / ($originalTotal - $vendorCommissionAmount)) * 100;
            echo "\n  ✅ CORRECT: " . number_format($percentRemaining, 1) . "% of order remains\n";
        }
    } else {
        echo "No refunds for this vendor.\n";
        echo "\n  ╔═══════════════════════════════════════════════════════╗\n";
        echo "  ║  FINAL REMAINING: " . str_pad(number_format($remainingBeforeRefund, 2) . " EGP", 35) . "║\n";
        echo "  ╚═══════════════════════════════════════════════════════╝\n";
    }
    
    echo "\n\n";
}

// Check accounting entries
echo "┌─────────────────────────────────────────────────────────────┐\n";
echo "│ ACCOUNTING ENTRIES                                          │\n";
echo "└─────────────────────────────────────────────────────────────┘\n\n";

$accountingEntries = \Modules\Accounting\app\Models\AccountingEntry::where('order_id', 253)
    ->orderBy('created_at', 'asc')
    ->get();

foreach ($accountingEntries as $entry) {
    $vendor = \Modules\Vendor\app\Models\Vendor::find($entry->vendor_id);
    $vendorName = $vendor ? $vendor->name : "N/A";
    
    echo "Entry #{$entry->id}:\n";
    echo "  Type: {$entry->type}\n";
    echo "  Vendor: {$vendorName} (#{$entry->vendor_id})\n";
    echo "  Amount: " . number_format($entry->amount, 2) . " EGP\n";
    echo "  Description: {$entry->description}\n";
    echo "  Created: {$entry->created_at}\n\n";
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "                    Verification Complete!                     \n";
echo "═══════════════════════════════════════════════════════════════\n";
