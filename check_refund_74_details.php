<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Refund #74 Details ===\n\n";

$refund = \Modules\Refund\app\Models\RefundRequest::find(74);

if (!$refund) {
    echo "❌ Refund not found\n";
    exit(1);
}

echo "Refund Number: {$refund->refund_number}\n";
echo "Order ID: {$refund->order_id}\n";
echo "Vendor ID: {$refund->vendor_id}\n";
echo "Status: {$refund->status}\n\n";

echo "=== Amounts ===\n";
echo "Total Products Amount: {$refund->total_products_amount} EGP\n";
echo "Total Shipping Amount: {$refund->total_shipping_amount} EGP\n";
echo "Total Tax Amount: {$refund->total_tax_amount} EGP\n";
echo "Vendor Fees Amount: {$refund->vendor_fees_amount} EGP\n";
echo "Vendor Discounts Amount: {$refund->vendor_discounts_amount} EGP\n";
echo "Promo Code Amount: {$refund->promo_code_amount} EGP\n";
echo "Points Used: {$refund->points_used} EGP\n";
echo "Return Shipping Cost: {$refund->return_shipping_cost} EGP\n";
echo "Total Refund Amount (customer): {$refund->total_refund_amount} EGP\n\n";

// Calculate vendor deduction
$vendorDeduction = $refund->total_products_amount 
    + $refund->total_shipping_amount 
    - ($refund->return_shipping_cost ?? 0);

echo "Vendor Deduction: {$vendorDeduction} EGP\n\n";

// Get refund items
echo "=== Refund Items ===\n";
$items = $refund->items()->with('orderProduct')->get();

foreach ($items as $item) {
    echo "Item #{$item->id}:\n";
    echo "  Order Product ID: {$item->order_product_id}\n";
    echo "  Quantity: {$item->quantity}\n";
    echo "  Total Price: {$item->total_price} EGP\n";
    echo "  Shipping Amount: {$item->shipping_amount} EGP\n";
    echo "  Tax Amount: {$item->tax_amount} EGP\n";
    
    if ($item->orderProduct) {
        $op = $item->orderProduct;
        echo "  Order Product Details:\n";
        echo "    Price: {$op->price} EGP\n";
        echo "    Shipping: {$op->shipping_cost} EGP\n";
        echo "    Commission: {$op->commission}%\n";
        
        $itemTotal = $item->total_price + $item->shipping_amount;
        $itemCommission = ($itemTotal * $op->commission) / 100;
        echo "    Item Commission: {$itemCommission} EGP\n";
    }
    echo "\n";
}

// Check calculation
echo "=== Calculation Check ===\n";

$expectedCustomerRefund = $refund->total_products_amount 
    + $refund->total_shipping_amount 
    + ($refund->vendor_fees_amount ?? 0)
    - ($refund->vendor_discounts_amount ?? 0)
    - ($refund->promo_code_amount ?? 0)
    - ($refund->points_used ?? 0)
    - ($refund->return_shipping_cost ?? 0);

echo "Expected Customer Refund:\n";
echo "  Products: {$refund->total_products_amount}\n";
echo "  + Shipping: {$refund->total_shipping_amount}\n";
echo "  + Fees: " . ($refund->vendor_fees_amount ?? 0) . "\n";
echo "  - Discounts: " . ($refund->vendor_discounts_amount ?? 0) . "\n";
echo "  - Promo: " . ($refund->promo_code_amount ?? 0) . "\n";
echo "  - Points: " . ($refund->points_used ?? 0) . "\n";
echo "  - Return Shipping: " . ($refund->return_shipping_cost ?? 0) . "\n";
echo "  = {$expectedCustomerRefund} EGP\n\n";

echo "Actual Customer Refund: {$refund->total_refund_amount} EGP\n";

if (abs($refund->total_refund_amount - $expectedCustomerRefund) < 0.01) {
    echo "✅ Customer refund is CORRECT!\n\n";
} else {
    echo "❌ Customer refund is WRONG! Difference: " . ($refund->total_refund_amount - $expectedCustomerRefund) . "\n\n";
}

// Check vendor deduction
$expectedVendorDeduction = $refund->total_products_amount + $refund->total_shipping_amount;

echo "Expected Vendor Deduction: {$expectedVendorDeduction} EGP\n";
echo "Actual Vendor Deduction: {$vendorDeduction} EGP\n";

if (abs($vendorDeduction - $expectedVendorDeduction) < 0.01) {
    echo "✅ Vendor deduction is CORRECT!\n\n";
} else {
    echo "❌ Vendor deduction is WRONG! Difference: " . ($vendorDeduction - $expectedVendorDeduction) . "\n\n";
}

// Check if refund is for partial quantity
$orderProduct = \Modules\Order\app\Models\OrderProduct::find($items->first()->order_product_id);
if ($orderProduct) {
    echo "=== Original Order Product ===\n";
    echo "Original Price: {$orderProduct->price} EGP\n";
    echo "Original Quantity: {$orderProduct->quantity}\n";
    echo "Original Shipping: {$orderProduct->shipping_cost} EGP\n";
    echo "Original Total: " . ($orderProduct->price + $orderProduct->shipping_cost) . " EGP\n\n";
    
    $refundedQty = $items->first()->quantity;
    echo "Refunded Quantity: {$refundedQty}\n";
    
    if ($refundedQty < $orderProduct->quantity) {
        echo "⚠️ This is a PARTIAL refund!\n";
        echo "Refunded: {$refundedQty} / {$orderProduct->quantity}\n\n";
    } else {
        echo "This is a FULL refund\n\n";
    }
}

// Show what should happen when status changes to 'refunded'
echo "=== When Status = 'refunded' ===\n";
echo "Vendor 107 remaining will be:\n";
echo "  Current: 629 EGP\n";
echo "  - Vendor Deduction: {$vendorDeduction} EGP\n";
echo "  + Refunded Commission: " . (($vendorDeduction * 15) / 100) . " EGP\n";
echo "  = New Remaining: " . (629 - $vendorDeduction + (($vendorDeduction * 15) / 100)) . " EGP\n";
