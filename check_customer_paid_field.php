<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\Order\app\Models\Order;

$order = Order::find(254);

echo "Order #254 customer_paid field:\n";
echo "================================\n";
echo "Value: " . var_export($order->customer_paid, true) . "\n";
echo "Type: " . gettype($order->customer_paid) . "\n";
echo "Is null: " . ($order->customer_paid === null ? 'YES' : 'NO') . "\n";
echo "Is zero: " . ($order->customer_paid == 0 ? 'YES' : 'NO') . "\n";
echo "Strict zero: " . ($order->customer_paid === 0 ? 'YES' : 'NO') . "\n";
echo "String zero: " . ($order->customer_paid === '0' ? 'YES' : 'NO') . "\n";
echo "Float zero: " . ($order->customer_paid === 0.0 ? 'YES' : 'NO') . "\n";

echo "\nCondition check:\n";
echo "customer_paid !== null: " . ($order->customer_paid !== null ? 'TRUE' : 'FALSE') . "\n";
echo "customer_paid == 0: " . ($order->customer_paid == 0 ? 'TRUE' : 'FALSE') . "\n";
echo "Both conditions: " . (($order->customer_paid !== null && $order->customer_paid == 0) ? 'TRUE' : 'FALSE') . "\n";
