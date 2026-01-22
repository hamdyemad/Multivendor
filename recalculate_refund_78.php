<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$refund = \Modules\Refund\app\Models\RefundRequest::find(78);

if ($refund) {
    echo "=== Before Recalculation ===" . PHP_EOL;
    echo "Total Refund Amount: " . $refund->total_refund_amount . " EGP" . PHP_EOL;
    echo "Points Used: " . $refund->points_used . " EGP" . PHP_EOL;
    echo "Promo Code: " . $refund->promo_code_amount . " EGP" . PHP_EOL;
    
    $refund->calculateTotals();
    
    echo PHP_EOL . "=== After Recalculation ===" . PHP_EOL;
    echo "Total Refund Amount: " . $refund->total_refund_amount . " EGP" . PHP_EOL;
    echo PHP_EOL;
    echo "Calculation:" . PHP_EOL;
    echo "Products: " . $refund->total_products_amount . PHP_EOL;
    echo "+ Shipping: " . $refund->total_shipping_amount . PHP_EOL;
    echo "- Promo Code: " . $refund->promo_code_amount . PHP_EOL;
    echo "- Points Used: " . $refund->points_used . PHP_EOL;
    echo "= " . $refund->total_refund_amount . " EGP (max with 0)" . PHP_EOL;
    
    echo PHP_EOL . "✅ Refund recalculated successfully!" . PHP_EOL;
} else {
    echo "❌ Refund not found!" . PHP_EOL;
}
