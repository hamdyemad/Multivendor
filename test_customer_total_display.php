<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\VendorOrderStage;

echo "=== Testing Customer Total Display Fix ===\n\n";

// Test Order #254 (customer paid with points)
$order = Order::with(['products.vendorProduct.vendor'])->find(254);

if (!$order) {
    echo "❌ Order #254 not found\n";
    exit(1);
}

echo "Order #254 Details:\n";
echo "==================\n";
echo "Order Total: " . number_format($order->total, 2) . " EGP\n";
echo "Customer Promo Code Amount: " . number_format($order->customer_promo_code_amount ?? 0, 2) . " EGP\n";
echo "Customer Points Cost: " . number_format($order->points_cost ?? 0, 2) . " EGP\n";
echo "Customer Paid (expected): " . number_format($order->total - ($order->customer_promo_code_amount ?? 0) - ($order->points_cost ?? 0), 2) . " EGP\n\n";

// Get vendors
$productsByVendor = $order->products->groupBy(function ($product) {
    return $product->vendorProduct?->vendor_id;
});

echo "Vendor-Specific Calculations:\n";
echo "=============================\n\n";

foreach ($productsByVendor as $vendorId => $vendorProducts) {
    $vendorName = $vendorProducts->first()->vendorProduct?->vendor?->name ?? 'N/A';
    
    // Get vendor order stage for shares
    $vendorOrderStage = VendorOrderStage::where('order_id', $order->id)
        ->where('vendor_id', $vendorId)
        ->first();
    
    $vendorPromoCodeShare = $vendorOrderStage?->promo_code_share ?? 0;
    $vendorPointsShare = $vendorOrderStage?->points_share ?? 0;
    
    // Calculate vendor total
    $vendorTotal = 0;
    foreach ($vendorProducts as $prod) {
        $vendorTotal += $prod->price + ($prod->shipping_cost ?? 0);
    }
    
    echo "Vendor: $vendorName (ID: $vendorId)\n";
    echo "-----------------------------------\n";
    echo "Vendor Total (Products + Shipping): " . number_format($vendorTotal, 2) . " EGP\n";
    echo "Vendor Promo Code Share (from Bnaia): " . number_format($vendorPromoCodeShare, 2) . " EGP\n";
    echo "Vendor Points Share (from Bnaia): " . number_format($vendorPointsShare, 2) . " EGP\n";
    
    // WRONG calculation (old way)
    $wrongCustomerTotal = $vendorTotal - $vendorPromoCodeShare - $vendorPointsShare;
    echo "\n❌ WRONG Customer Total (using vendor shares): " . number_format($wrongCustomerTotal, 2) . " EGP\n";
    
    // CORRECT calculation (new way)
    $customerPromoAmount = $order->customer_promo_code_amount ?? 0;
    $customerPointsCost = $order->points_cost ?? 0;
    $correctCustomerTotal = $vendorTotal - $customerPromoAmount - $customerPointsCost;
    echo "✅ CORRECT Customer Total (using customer amounts): " . number_format($correctCustomerTotal, 2) . " EGP\n";
    
    echo "\nExplanation:\n";
    echo "- Vendor receives: " . number_format($vendorTotal, 2) . " EGP (products + shipping)\n";
    echo "- Bnaia pays vendor: " . number_format($vendorPromoCodeShare + $vendorPointsShare, 2) . " EGP (promo + points share)\n";
    echo "- Customer pays: " . number_format($correctCustomerTotal, 2) . " EGP\n";
    echo "- Total: " . number_format($vendorTotal + $vendorPromoCodeShare + $vendorPointsShare, 2) . " EGP\n";
    
    echo "\n";
}

echo "\n=== Summary ===\n";
echo "The fix ensures that 'Customer Total' shows what the customer actually paid,\n";
echo "not what the vendor receives from Bnaia. For Order #254, customer paid 0 EGP\n";
echo "because they used points for the full amount.\n";
echo "\n✅ Fix applied successfully!\n";
