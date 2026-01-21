<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$item = \Modules\Refund\app\Models\RefundRequestItem::with([
    'orderProduct.vendorProductVariant.variantConfiguration.key'
])->find(27);

if (!$item) {
    echo "Item not found!\n";
    exit;
}

$variant = $item->orderProduct->vendorProductVariant;

echo "=== VARIANT INFO ===\n";
echo "Variant ID: {$variant->id}\n";
echo "SKU: {$variant->sku}\n";
echo "Has variantConfiguration relation loaded: " . ($variant->relationLoaded('variantConfiguration') ? 'YES' : 'NO') . "\n";

if ($variant->relationLoaded('variantConfiguration')) {
    $config = $variant->variantConfiguration;
    if ($config) {
        echo "Configuration ID: {$config->id}\n";
        echo "Configuration Name: {$config->name}\n";
        echo "Configuration Type: {$config->type}\n";
        echo "Has key relation loaded: " . ($config->relationLoaded('key') ? 'YES' : 'NO') . "\n";
        if ($config->relationLoaded('key') && $config->key) {
            echo "Key ID: {$config->key->id}\n";
            echo "Key Name: {$config->key->name}\n";
        }
    } else {
        echo "Configuration is NULL (product has no variant configuration)\n";
    }
}
