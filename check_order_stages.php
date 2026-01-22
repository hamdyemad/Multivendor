<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Order Stages:\n";
echo str_repeat("─", 50) . "\n";

$stages = \Modules\Order\app\Models\OrderStage::withoutCountryFilter()->get();

foreach ($stages as $stage) {
    echo "ID: {$stage->id}\n";
    echo "Type: {$stage->type}\n";
    echo "Name: " . json_encode($stage->name) . "\n\n";
}
