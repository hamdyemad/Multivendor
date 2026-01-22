<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$order = \Modules\Order\app\Models\Order::with(['products.vendorProduct.product', 'products.taxes'])->find(253);

echo "Order #253 Full Analysis\n";
echo "========================\n\n";

echo "Order Total: {$order->total_price} EGP\n";
echo "Customer Paid: {$order->customer_paid} EGP\n\n";

// Get all vendors in this order
$vendors = $order->products->groupBy('vendor_id');

foreach ($vendors as $vendorId => $products) {
    $vendor = \Modules\Vendor\app\Models\Vendor::find($vendorId);
    echo "Vendor #{$vendorId} ({$vendor->name})\n";
    echo str_repeat("=", 50) . "\n";
    
    $vendorTotal = 0;
    $vendorShipping = 0;
    $vendorCommission = 0;
    
    foreach ($products as $product) {
        echo "  Product #{$product->id}: {$product->vendorProduct->product->name}\n";
        echo "    Quantity: {$product->quantity}\n";
        echo "    Price: {$product->price} EGP\n";
        echo "    Shipping: {$product->shipping_cost} EGP\n";
        echo "    Commission: {$product->commission}%\n";
        
        $vendorTotal += $product->price;
        $vendorShipping += $product->shipping_cost;
        $vendorCommission += ($product->price + $product->shipping_cost) * ($product->commission / 100);
    }
    
    echo "\n  Vendor Totals:\n";
    echo "    Products: {$vendorTotal} EGP\n";
    echo "    Shipping: {$vendorShipping} EGP\n";
    echo "    Total: " . ($vendorTotal + $vendorShipping) . " EGP\n";
    echo "    Commission: " . number_format($vendorCommission, 2) . " EGP\n";
    echo "    Remaining (before refunds): " . number_format(($vendorTotal + $vendorShipping) - $vendorCommission, 2) . " EGP\n";
    
    // Get refunds for this vendor
    $refunds = \Modules\Refund\app\Models\RefundRequest::where('order_id', 253)
        ->where('vendor_id', $vendorId)
        ->whereIn('status', ['refunded'])
        ->with('items')
        ->get();
    
    if ($refunds->count() > 0) {
        echo "\n  Refunds:\n";
        $totalRefunded = 0;
        $totalRefundedCommission = 0;
        
        foreach ($refunds as $refund) {
            echo "    Refund #{$refund->id} (Status: {$refund->status})\n";
            echo "      Customer Refund Amount: {$refund->total_refund_amount} EGP\n";
            
            // Calculate vendor deduction
            $vendorDeduction = $refund->total_products_amount 
                + $refund->total_shipping_amount 
                - ($refund->return_shipping_cost ?? 0);
            
            echo "      Vendor Deduction: " . number_format($vendorDeduction, 2) . " EGP\n";
            
            // Calculate refunded commission
            foreach ($refund->items as $item) {
                $orderProduct = $item->orderProduct;
                $itemTotal = $item->total_price + $item->shipping_amount;
                $itemCommission = $itemTotal * ($orderProduct->commission / 100);
                $totalRefundedCommission += $itemCommission;
                echo "        Item: Product #{$orderProduct->id}, Qty {$item->quantity}, Commission: " . number_format($itemCommission, 2) . " EGP\n";
            }
            
            $totalRefunded += $vendorDeduction;
        }
        
        echo "\n  Total Refunded: " . number_format($totalRefunded, 2) . " EGP\n";
        echo "  Total Refunded Commission: " . number_format($totalRefundedCommission, 2) . " EGP\n";
        echo "  Final Remaining: " . number_format(($vendorTotal + $vendorShipping) - $vendorCommission - $totalRefunded + $totalRefundedCommission, 2) . " EGP\n";
    }
    
    echo "\n";
}

// Check accounting entries
echo "\nAccounting Entries for Order #253\n";
echo str_repeat("=", 50) . "\n";

$accountingEntries = \Modules\Accounting\app\Models\AccountingEntry::where('order_id', 253)
    ->orderBy('created_at', 'desc')
    ->get();

foreach ($accountingEntries as $entry) {
    echo "Entry #{$entry->id}: {$entry->type} - {$entry->description}\n";
    echo "  Vendor: {$entry->vendor_id}\n";
    echo "  Amount: {$entry->amount} EGP\n";
    echo "  Created: {$entry->created_at}\n\n";
}
