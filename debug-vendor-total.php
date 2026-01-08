<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VENDOR TOTAL DEBUG ===\n\n";

// Get the order - find by order_number
$orderNumber = 'ORD-000007';
$order = \Modules\Order\app\Models\Order::with(['products', 'vendorStages'])->where('order_number', $orderNumber)->first();

if (!$order) {
    echo "Order not found!\n";
    exit;
}

echo "Order ID: {$order->id}\n";
echo "Order Number: {$order->order_number}\n";
echo "Total Price: {$order->total_price}\n\n";

// Get vendor ID (assuming first vendor for testing)
$vendorId = $order->products->first()->vendor_id ?? null;

if (!$vendorId) {
    echo "No vendor found!\n";
    exit;
}

echo "Vendor ID: {$vendorId}\n\n";

// Get vendor products
$vendorProducts = $order->products->where('vendor_id', $vendorId);

echo "=== VENDOR PRODUCTS ===\n";
$vendorProductTotal = 0;
foreach ($vendorProducts as $product) {
    echo "Product: {$product->name}\n";
    echo "  Price (with tax, total): {$product->price}\n";
    echo "  Quantity: {$product->quantity}\n";
    echo "  Shipping Cost: {$product->shipping_cost}\n";
    $vendorProductTotal += $product->price;
}
echo "Vendor Product Total: {$vendorProductTotal}\n\n";

// Get vendor shipping
$vendorShipping = $vendorProducts->sum('shipping_cost');
echo "Vendor Shipping: {$vendorShipping}\n\n";

// Get vendor order stage
$vendorOrderStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $order->id)
    ->where('vendor_id', $vendorId)
    ->first();

if ($vendorOrderStage) {
    echo "=== VENDOR ORDER STAGE ===\n";
    echo "Promo Code Share: {$vendorOrderStage->promo_code_share}\n";
    echo "Points Share: {$vendorOrderStage->points_share}\n";
    echo "Fees Share: {$vendorOrderStage->fees_share}\n";
    echo "Discounts Share: {$vendorOrderStage->discounts_share}\n\n";
    
    $promoCodeShare = $vendorOrderStage->promo_code_share ?? 0;
    $pointsShare = $vendorOrderStage->points_share ?? 0;
    $feesShare = $vendorOrderStage->fees_share ?? 0;
    $discountsShare = $vendorOrderStage->discounts_share ?? 0;
    
    echo "=== CALCULATION ===\n";
    echo "Products: {$vendorProductTotal}\n";
    echo "+ Shipping: {$vendorShipping}\n";
    echo "+ Fees: {$feesShare}\n";
    echo "- Promo Code: {$promoCodeShare}\n";
    echo "- Points: {$pointsShare}\n";
    echo "- Discounts: {$discountsShare}\n";
    
    $total = $vendorProductTotal + $vendorShipping + $feesShare - $promoCodeShare - $pointsShare - $discountsShare;
    echo "= TOTAL: {$total}\n\n";
} else {
    echo "No vendor order stage found!\n";
}

// Check order fees and discounts
echo "=== ORDER FEES & DISCOUNTS ===\n";
$extraFeesDiscounts = \DB::table('extra_fees_discounts')->where('order_id', $order->id)->get();
foreach ($extraFeesDiscounts as $item) {
    echo "{$item->type}: {$item->reason} = {$item->cost}\n";
}

echo "\n=== ORDER TOTALS ===\n";
echo "Total Product Price: {$order->total_product_price}\n";
echo "Shipping: {$order->shipping}\n";
echo "Total Price: {$order->total_price}\n";
echo "Promo Code Amount: {$order->customer_promo_code_amount}\n";
echo "Points Amount: {$order->customer_points_amount}\n";
