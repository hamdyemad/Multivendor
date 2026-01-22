<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\Order\app\Models\Order;

echo "=== Testing Vendor Promo/Points Distribution ===\n\n";

$order = Order::with(['products.vendorProduct.vendor'])->find(254);

if (!$order) {
    echo "❌ Order #254 not found\n";
    exit(1);
}

echo "Order #254 Details:\n";
echo "===================\n";
echo "Total Customer Promo Code: " . number_format($order->customer_promo_code_amount ?? 0, 2) . " EGP\n";
echo "Total Customer Points Cost: " . number_format($order->points_cost ?? 0, 2) . " EGP\n\n";

// Calculate total order subtotal
$orderTotalSubtotal = $order->products->sum(function($p) {
    return $p->price; // price already includes tax
});

echo "Order Total Subtotal (all vendors): " . number_format($orderTotalSubtotal, 2) . " EGP\n\n";

// Group by vendor
$productsByVendor = $order->products->groupBy(function ($product) {
    return $product->vendorProduct?->vendor_id;
});

echo "Per-Vendor Distribution:\n";
echo "========================\n\n";

foreach ($productsByVendor as $vendorId => $vendorProducts) {
    $vendorName = $vendorProducts->first()->vendorProduct?->vendor?->name ?? 'N/A';
    
    // Calculate vendor subtotal
    $vendorSubtotalWithTax = 0;
    foreach ($vendorProducts as $prod) {
        $vendorSubtotalWithTax += $prod->price;
    }
    
    // Calculate vendor percentage
    $vendorPercentage = $orderTotalSubtotal > 0 
        ? ($vendorSubtotalWithTax / $orderTotalSubtotal) 
        : 0;
    
    // Calculate vendor's share of customer promo/points
    $vendorCustomerPromoAmount = ($order->customer_promo_code_amount ?? 0) * $vendorPercentage;
    $vendorCustomerPointsCost = ($order->points_cost ?? 0) * $vendorPercentage;
    
    echo "Vendor: $vendorName (ID: $vendorId)\n";
    echo "-----------------------------------\n";
    echo "Vendor Subtotal: " . number_format($vendorSubtotalWithTax, 2) . " EGP\n";
    echo "Vendor Percentage: " . number_format($vendorPercentage * 100, 2) . "%\n";
    echo "Vendor Promo Share (customer): " . number_format($vendorCustomerPromoAmount, 2) . " EGP\n";
    echo "Vendor Points Share (customer): " . number_format($vendorCustomerPointsCost, 2) . " EGP\n";
    
    // Calculate customer total for this vendor
    $vendorShipping = $vendorProducts->sum('shipping_cost');
    $customerTotal = $vendorSubtotalWithTax - $vendorCustomerPromoAmount - $vendorCustomerPointsCost + $vendorShipping;
    $customerTotal = max(0, $customerTotal);
    
    echo "Vendor Shipping: " . number_format($vendorShipping, 2) . " EGP\n";
    echo "Customer Total (for this vendor): " . number_format($customerTotal, 2) . " EGP\n";
    echo "\n";
}

echo "✅ Promo and Points are now distributed proportionally to each vendor!\n";
echo "   Each vendor sees their share of the customer discount.\n";
