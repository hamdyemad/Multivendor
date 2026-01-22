<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking Order #253 ===\n\n";

$order = \Modules\Order\app\Models\Order::find(253);

if (!$order) {
    echo "❌ Order #253 not found\n";
    exit(1);
}

echo "Order Number: {$order->order_number}\n";
echo "Customer ID: {$order->customer_id}\n";
echo "Total Price: {$order->total_price} EGP\n";
echo "Customer Promo Code: {$order->customer_promo_code_amount} EGP\n";
echo "Points Cost: {$order->points_cost} EGP\n";
echo "Customer Points Used: {$order->customer_points_used}\n\n";

// Get order products grouped by vendor
$products = \Modules\Order\app\Models\OrderProduct::where('order_id', 253)->get();

echo "=== Order Products ===\n";
$vendorTotals = [];

foreach ($products as $product) {
    echo "Product #{$product->id}:\n";
    echo "  Vendor: {$product->vendor_id}\n";
    echo "  Price: {$product->price} EGP\n";
    echo "  Shipping: {$product->shipping_cost} EGP\n";
    echo "  Commission: {$product->commission}%\n";
    echo "  Total: " . ($product->price + $product->shipping_cost) . " EGP\n\n";
    
    if (!isset($vendorTotals[$product->vendor_id])) {
        $vendorTotals[$product->vendor_id] = [
            'products_total' => 0,
            'shipping_total' => 0,
            'commission_total' => 0,
        ];
    }
    
    $productTotal = $product->price + $product->shipping_cost;
    $vendorTotals[$product->vendor_id]['products_total'] += $product->price;
    $vendorTotals[$product->vendor_id]['shipping_total'] += $product->shipping_cost;
    $vendorTotals[$product->vendor_id]['commission_total'] += ($productTotal * $product->commission) / 100;
}

// Get vendor order stages
echo "=== Vendor Order Stages ===\n";
$vendorStages = \Modules\Order\app\Models\VendorOrderStage::where('order_id', 253)->get();

foreach ($vendorStages as $stage) {
    echo "Vendor {$stage->vendor_id}:\n";
    echo "  Promo Code Share: {$stage->promo_code_share} EGP\n";
    echo "  Points Share: {$stage->points_share} EGP\n";
    echo "  Total Shares: " . ($stage->promo_code_share + $stage->points_share) . " EGP\n\n";
}

// Get refunds
echo "=== Refund Requests ===\n";
$refunds = \Modules\Refund\app\Models\RefundRequest::where('order_id', 253)
    ->with('items.orderProduct')
    ->get();

if ($refunds->isEmpty()) {
    echo "No refunds found\n\n";
} else {
    foreach ($refunds as $refund) {
        echo "Refund #{$refund->id} - {$refund->refund_number}:\n";
        echo "  Vendor: {$refund->vendor_id}\n";
        echo "  Status: {$refund->status}\n";
        echo "  Total Products Amount: {$refund->total_products_amount} EGP\n";
        echo "  Total Shipping Amount: {$refund->total_shipping_amount} EGP\n";
        echo "  Promo Code Amount: {$refund->promo_code_amount} EGP\n";
        echo "  Points Used: {$refund->points_used} EGP\n";
        echo "  Total Refund Amount (customer): {$refund->total_refund_amount} EGP\n";
        
        $vendorDeduction = $refund->total_products_amount 
            + $refund->total_shipping_amount 
            - ($refund->return_shipping_cost ?? 0);
        echo "  Vendor Deduction: {$vendorDeduction} EGP\n\n";
        
        echo "  Items:\n";
        foreach ($refund->items as $item) {
            $orderProduct = $item->orderProduct;
            echo "    - Product #{$item->order_product_id}: {$item->total_price} + {$item->shipping_amount} = " . ($item->total_price + $item->shipping_amount) . " EGP\n";
            if ($orderProduct) {
                echo "      Commission: {$orderProduct->commission}%\n";
            }
        }
        echo "\n";
    }
}

// Calculate remaining for each vendor
echo "=== Vendor Remaining Calculations ===\n\n";

foreach ($vendorTotals as $vendorId => $totals) {
    echo "Vendor {$vendorId}:\n";
    
    $vendorTotal = $totals['products_total'] + $totals['shipping_total'];
    $commission = $totals['commission_total'];
    
    echo "  Products: {$totals['products_total']} EGP\n";
    echo "  Shipping: {$totals['shipping_total']} EGP\n";
    echo "  Total: {$vendorTotal} EGP\n";
    echo "  Commission: {$commission} EGP\n";
    
    // Get vendor stage
    $vendorStage = $vendorStages->where('vendor_id', $vendorId)->first();
    if ($vendorStage) {
        echo "  Promo Code Share: {$vendorStage->promo_code_share} EGP\n";
        echo "  Points Share: {$vendorStage->points_share} EGP\n";
    }
    
    // Get refunds for this vendor
    $vendorRefunds = $refunds->where('vendor_id', $vendorId)->where('status', 'refunded');
    $refundedAmount = 0;
    $refundedCommission = 0;
    
    foreach ($vendorRefunds as $refund) {
        $vendorDeduction = $refund->total_products_amount 
            + $refund->total_shipping_amount 
            - ($refund->return_shipping_cost ?? 0);
        $refundedAmount += $vendorDeduction;
        
        // Calculate refunded commission
        foreach ($refund->items as $item) {
            $orderProduct = $item->orderProduct;
            if ($orderProduct) {
                $itemTotal = $item->total_price + $item->shipping_amount;
                $itemCommission = ($itemTotal * $orderProduct->commission) / 100;
                $refundedCommission += $itemCommission;
            }
        }
    }
    
    echo "  Refunded Amount: {$refundedAmount} EGP\n";
    echo "  Refunded Commission: {$refundedCommission} EGP\n";
    
    $remainingBeforeRefund = $vendorTotal - $commission;
    $netRefundImpact = $refundedAmount - $refundedCommission;
    $remaining = $remainingBeforeRefund - $netRefundImpact;
    
    echo "\n  === Calculation ===\n";
    echo "  Total: {$vendorTotal} EGP\n";
    echo "  - Commission: {$commission} EGP\n";
    echo "  = Remaining Before Refund: {$remainingBeforeRefund} EGP\n";
    echo "  - Net Refund Impact: {$netRefundImpact} EGP\n";
    echo "  = Final Remaining: {$remaining} EGP\n\n";
    
    // Verify
    if ($refundedAmount > 0) {
        $expectedRemaining = $vendorTotal - $commission - $refundedAmount + $refundedCommission;
        echo "  Expected Remaining: {$expectedRemaining} EGP\n";
        
        if (abs($remaining - $expectedRemaining) < 0.01) {
            echo "  ✅ Calculation is CORRECT!\n\n";
        } else {
            echo "  ❌ Calculation is WRONG! Difference: " . ($remaining - $expectedRemaining) . "\n\n";
        }
    }
}

echo "=== Summary ===\n";
echo "Order Total: " . $products->sum(function($p) { return $p->price + $p->shipping_cost; }) . " EGP\n";
echo "Customer Paid: {$order->total_price} EGP\n";
echo "Promo Code: {$order->customer_promo_code_amount} EGP\n";
echo "Points: {$order->points_cost} EGP\n";

$totalRefunded = $refunds->where('status', 'refunded')->sum('total_refund_amount');
$totalVendorDeduction = 0;
foreach ($refunds->where('status', 'refunded') as $refund) {
    $totalVendorDeduction += $refund->total_products_amount 
        + $refund->total_shipping_amount 
        - ($refund->return_shipping_cost ?? 0);
}

echo "Total Refunded (customer): {$totalRefunded} EGP\n";
echo "Total Vendor Deduction: {$totalVendorDeduction} EGP\n";
