<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Find a product with variant configuration
$variantWithConfig = \Modules\CatalogManagement\app\Models\VendorProductVariant::whereHas('variantConfiguration')
    ->with('variantConfiguration.key')
    ->first();

if (!$variantWithConfig) {
    echo "No variant with configuration found!\n";
    exit;
}

echo "=== VARIANT WITH CONFIGURATION ===\n";
echo "Variant ID: {$variantWithConfig->id}\n";
echo "SKU: {$variantWithConfig->sku}\n";
echo "Configuration ID: {$variantWithConfig->variantConfiguration->id}\n";
echo "Configuration Name: {$variantWithConfig->variantConfiguration->name}\n";
echo "Key Name: {$variantWithConfig->variantConfiguration->key->name}\n";
echo "\n";

// Find an order product with this variant
$orderProduct = \Modules\Order\app\Models\OrderProduct::where('vendor_product_variant_id', $variantWithConfig->id)
    ->whereHas('order', function($q) {
        $q->whereHas('vendorStages', function($sq) {
            $sq->whereHas('stage', function($ssq) {
                $ssq->where('type', 'delivered');
            });
        });
    })
    ->first();

if ($orderProduct) {
    echo "Found order product ID: {$orderProduct->id}\n";
    echo "Order ID: {$orderProduct->order_id}\n";
    echo "Quantity: {$orderProduct->quantity}\n";
} else {
    echo "No delivered order found with this variant\n";
}
