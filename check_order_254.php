<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "═══════════════════════════════════════════════════════════════\n";
echo "           Order #254 Analysis                                 \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$order = \Modules\Order\app\Models\Order::with(['products.vendorProduct.product', 'products.taxes'])->find(254);

if (!$order) {
    echo "Order #254 not found!\n";
    exit;
}

echo "Order Number: {$order->order_number}\n";
echo "Order Total: {$order->total_price} EGP\n";
echo "Customer Paid: {$order->customer_paid} EGP\n";
echo "Points Used: {$order->points_used} EGP\n";
echo "Promo Code: {$order->promo_code_amount} EGP\n\n";

echo "ORDER PRODUCTS:\n";
echo str_repeat("─", 63) . "\n";

foreach ($order->products as $product) {
    $vendor = \Modules\Vendor\app\Models\Vendor::find($product->vendor_id);
    
    echo "Product #{$product->id}: {$product->vendorProduct->product->name}\n";
    echo "  Vendor: {$vendor->name} (#{$product->vendor_id})\n";
    echo "  Quantity: {$product->quantity}\n";
    echo "  Price: {$product->price} EGP\n";
    echo "  Shipping: {$product->shipping_cost} EGP\n";
    echo "  Commission: {$product->commission}%\n";
    
    // Get department commission
    $vendorProduct = $product->vendorProduct;
    $department = $vendorProduct->product->subCategory->category->department ?? null;
    $departmentCommission = $department ? $department->commission : 0;
    
    echo "  Department Commission: {$departmentCommission}%\n";
    
    // Calculate what commission should be
    $totalWithShipping = $product->price + $product->shipping_cost;
    $actualCommission = ($totalWithShipping * $product->commission) / 100;
    $expectedCommission = ($totalWithShipping * $departmentCommission) / 100;
    
    echo "  Actual Commission Amount: " . number_format($actualCommission, 2) . " EGP\n";
    echo "  Expected Commission Amount: " . number_format($expectedCommission, 2) . " EGP\n";
    
    if ($product->commission == 0 && $departmentCommission > 0) {
        echo "  ⚠️  WARNING: Commission is 0% but department has {$departmentCommission}%\n";
    }
    
    echo "\n";
}

// Check vendor_order_stages
echo "VENDOR ORDER STAGES:\n";
echo str_repeat("─", 63) . "\n";

$vendorStages = \Modules\Order\app\Models\VendorOrderStage::where('order_id', 254)
    ->with('vendor')
    ->get();

foreach ($vendorStages as $stage) {
    echo "Vendor: {$stage->vendor->name} (#{$stage->vendor_id})\n";
    echo "  Promo Code Share: {$stage->promo_code_share} EGP\n";
    echo "  Points Share: {$stage->points_share} EGP\n";
    echo "  Stage: {$stage->stage->name}\n\n";
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "                    Analysis Complete!                         \n";
echo "═══════════════════════════════════════════════════════════════\n";
