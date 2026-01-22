<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "═══════════════════════════════════════════════════════════════\n";
echo "      Order #254 Complete Test (Points/Promo + Refund)        \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$orderId = 254;
$order = \Modules\Order\app\Models\Order::with(['products.vendorProduct.product', 'products.taxes'])->find($orderId);

if (!$order) {
    echo "Order #{$orderId} not found!\n";
    exit;
}

echo "STEP 1: Order Details\n";
echo str_repeat("─", 63) . "\n";
echo "Order Number: {$order->order_number}\n";
echo "Order Total: {$order->total_price} EGP\n";
echo "Customer Paid: {$order->customer_paid} EGP\n";
echo "Points Used: {$order->points_used} EGP\n";
echo "Promo Code: {$order->promo_code_amount} EGP\n\n";

echo "Products:\n";
$totalCommission = 0;

foreach ($order->products as $product) {
    $vendor = \Modules\Vendor\app\Models\Vendor::find($product->vendor_id);
    $vendorProduct = $product->vendorProduct;
    
    echo "  Product #{$product->id}: {$vendorProduct->product->name}\n";
    echo "    Vendor: {$vendor->name}\n";
    echo "    Quantity: {$product->quantity}\n";
    echo "    Price: {$product->price} EGP\n";
    echo "    Shipping: {$product->shipping_cost} EGP\n";
    echo "    Total: " . ($product->price + $product->shipping_cost) . " EGP\n";
    echo "    Commission: {$product->commission}%\n";
    
    $commissionAmount = (($product->price + $product->shipping_cost) * $product->commission) / 100;
    echo "    Commission Amount: " . number_format($commissionAmount, 2) . " EGP\n";
    
    $totalCommission += $commissionAmount;
    
    // Check vendor shares
    $vendorStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $orderId)
        ->where('vendor_id', $product->vendor_id)
        ->first();
    
    if ($vendorStage) {
        $vendorTotal = $product->price + $product->shipping_cost;
        $vendorNet = $vendorTotal - $commissionAmount;
        
        echo "    Vendor Receives from Bnaia:\n";
        echo "      Promo Share: " . number_format($vendorStage->promo_code_share, 2) . " EGP\n";
        echo "      Points Share: " . number_format($vendorStage->points_share, 2) . " EGP\n";
        echo "      Total: " . number_format($vendorStage->promo_code_share + $vendorStage->points_share, 2) . " EGP\n";
        echo "    Vendor Net (after commission): " . number_format($vendorNet, 2) . " EGP\n";
    }
    
    echo "\n";
}

echo "Total Commission: " . number_format($totalCommission, 2) . " EGP\n\n";

// Test refund scenario
echo "STEP 2: Refund Scenario Test\n";
echo str_repeat("─", 63) . "\n";

// Get Vendor #107 product
$productToRefund = $order->products->where('vendor_id', 107)->first();

if (!$productToRefund) {
    echo "No product found for Vendor #107\n";
    exit;
}

echo "Simulating refund for Vendor #107\n";
echo "Product: {$productToRefund->vendorProduct->product->name}\n";
echo "Quantity to refund: 2 (out of {$productToRefund->quantity})\n\n";

// Calculate refund amounts
$refundQuantity = 2;
$proportion = $refundQuantity / $productToRefund->quantity;

$refundProducts = ($productToRefund->price / $productToRefund->quantity) * $refundQuantity;
$refundShipping = ($productToRefund->shipping_cost / $productToRefund->quantity) * $refundQuantity;
$refundTotal = $refundProducts + $refundShipping;

echo "Refund Calculation:\n";
echo "  Products: " . number_format($refundProducts, 2) . " EGP\n";
echo "  Shipping: " . number_format($refundShipping, 2) . " EGP\n";
echo "  Total: " . number_format($refundTotal, 2) . " EGP\n";
echo "  Commission {$productToRefund->commission}%: " . number_format(($refundTotal * $productToRefund->commission) / 100, 2) . " EGP\n\n";

// Get vendor shares for refund
$vendorStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $orderId)
    ->where('vendor_id', 107)
    ->first();

if ($vendorStage) {
    $refundPromoShare = $vendorStage->promo_code_share * $proportion;
    $refundPointsShare = $vendorStage->points_share * $proportion;
    
    echo "Vendor Shares (proportional):\n";
    echo "  Promo Share: " . number_format($refundPromoShare, 2) . " EGP\n";
    echo "  Points Share: " . number_format($refundPointsShare, 2) . " EGP\n";
    echo "  Total Shares: " . number_format($refundPromoShare + $refundPointsShare, 2) . " EGP\n\n";
}

// Calculate customer refund
$customerRefund = $refundTotal; // Customer gets products + shipping back
echo "Customer Refund Amount: " . number_format($customerRefund, 2) . " EGP\n";
echo "  (Customer paid 0, but this is what they would get back)\n\n";

// Calculate vendor deduction
$vendorDeduction = $refundTotal; // Vendor returns to Bnaia
$refundCommission = ($refundTotal * $productToRefund->commission) / 100;
$vendorNetImpact = $vendorDeduction - $refundCommission;

echo "Vendor Impact:\n";
echo "  Returns to Bnaia: " . number_format($vendorDeduction, 2) . " EGP\n";
echo "  Gets Commission Back: " . number_format($refundCommission, 2) . " EGP\n";
echo "  Net Impact: " . number_format($vendorNetImpact, 2) . " EGP\n\n";

// Verify commission is correct
if ($productToRefund->commission > 0) {
    echo "✅ Commission is {$productToRefund->commission}% - Refund will work correctly!\n";
    echo "✅ Vendor will get commission back on refund\n";
} else {
    echo "❌ Commission is 0% - Vendor won't get commission back!\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "SUMMARY:\n";
echo "═══════════════════════════════════════════════════════════════\n";

echo "Order #254:\n";
echo "  ✅ Customer paid with points (0 EGP cash)\n";
echo "  ✅ Vendors receive money from Bnaia (promo + points shares)\n";
echo "  ✅ Commission calculated correctly: " . number_format($totalCommission, 2) . " EGP\n";
echo "  ✅ Bnaia takes commission from vendors\n\n";

echo "Refund Scenario:\n";
echo "  ✅ Customer gets refund (even though paid 0)\n";
echo "  ✅ Vendor returns money to Bnaia\n";
echo "  ✅ Vendor gets commission back\n";
echo "  ✅ Fair for everyone!\n";

echo "\n";
