<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "═══════════════════════════════════════════════════════════════\n";
echo "           Fix Order #254 Commission                           \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$orderId = 254;
$order = \Modules\Order\app\Models\Order::with(['products.vendorProduct.product'])->find($orderId);

if (!$order) {
    echo "Order #{$orderId} not found!\n";
    exit;
}

echo "Order Number: {$order->order_number}\n";
echo "Order Total: {$order->total_price} EGP\n";
echo "Points Used: {$order->points_used} EGP\n\n";

echo "FIXING COMMISSION FOR ORDER PRODUCTS:\n";
echo str_repeat("─", 63) . "\n\n";

\Illuminate\Support\Facades\DB::beginTransaction();

try {
    $totalCommissionBefore = 0;
    $totalCommissionAfter = 0;
    
    foreach ($order->products as $product) {
        $vendorProduct = $product->vendorProduct;
        $productModel = $vendorProduct->product;
        
        // Get department commission
        $department = $productModel->subCategory->category->department ?? null;
        $departmentCommission = $department ? $department->commission : 0;
        
        // Get activity commission (fallback)
        $activity = $department ? $department->activity : null;
        $activityCommission = $activity ? $activity->commission : 0;
        
        // Use department commission, fallback to activity
        $correctCommission = $departmentCommission > 0 ? $departmentCommission : $activityCommission;
        
        $vendor = \Modules\Vendor\app\Models\Vendor::find($product->vendor_id);
        
        echo "Product #{$product->id}: {$productModel->name}\n";
        echo "  Vendor: {$vendor->name} (#{$product->vendor_id})\n";
        echo "  Price + Shipping: " . ($product->price + $product->shipping_cost) . " EGP\n";
        echo "  Current Commission: {$product->commission}%\n";
        echo "  Department Commission: {$departmentCommission}%\n";
        echo "  Activity Commission: {$activityCommission}%\n";
        echo "  Correct Commission: {$correctCommission}%\n";
        
        $currentCommissionAmount = (($product->price + $product->shipping_cost) * $product->commission) / 100;
        $correctCommissionAmount = (($product->price + $product->shipping_cost) * $correctCommission) / 100;
        
        $totalCommissionBefore += $currentCommissionAmount;
        $totalCommissionAfter += $correctCommissionAmount;
        
        if ($product->commission != $correctCommission) {
            echo "  → Updating commission from {$product->commission}% to {$correctCommission}%\n";
            echo "  → Commission amount: " . number_format($currentCommissionAmount, 2) . " → " . number_format($correctCommissionAmount, 2) . " EGP\n";
            
            // Update the commission
            $product->commission = $correctCommission;
            $product->save();
            
            echo "  ✅ Updated!\n";
        } else {
            echo "  ✅ Already correct\n";
        }
        
        echo "\n";
    }
    
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "SUMMARY:\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "Total Commission Before: " . number_format($totalCommissionBefore, 2) . " EGP\n";
    echo "Total Commission After: " . number_format($totalCommissionAfter, 2) . " EGP\n";
    echo "Difference: " . number_format($totalCommissionAfter - $totalCommissionBefore, 2) . " EGP\n\n";
    
    // Ask for confirmation
    echo "Do you want to commit these changes? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($line) === 'yes' || strtolower($line) === 'y') {
        \Illuminate\Support\Facades\DB::commit();
        echo "\n✅ Changes committed successfully!\n";
        
        // Verify the changes
        echo "\nVERIFYING CHANGES:\n";
        echo str_repeat("─", 63) . "\n";
        
        $order->refresh();
        foreach ($order->products as $product) {
            $vendorProduct = $product->vendorProduct;
            $productModel = $vendorProduct->product;
            echo "Product #{$product->id}: Commission = {$product->commission}%\n";
        }
        
    } else {
        \Illuminate\Support\Facades\DB::rollBack();
        echo "\n❌ Changes rolled back. No changes made.\n";
    }
    
} catch (\Exception $e) {
    \Illuminate\Support\Facades\DB::rollBack();
    echo "\n❌ ERROR: {$e->getMessage()}\n";
    echo "Changes rolled back.\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "                    Process Complete!                          \n";
echo "═══════════════════════════════════════════════════════════════\n";
