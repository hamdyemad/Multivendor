<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING SHIPPING CALCULATION FOR CUSTOMER 13 ===\n\n";

// Get customer with addresses
$customer = \Modules\Customer\app\Models\Customer::with(['addresses'])->find(13);

if (!$customer || $customer->addresses->isEmpty()) {
    echo "Customer 13 not found or has no addresses!\n";
    exit;
}

$address = $customer->addresses->first();
echo "Using Address ID: {$address->id}\n";
echo "City ID: {$address->city_id}\n";
echo "City Name: " . ($address->city->name ?? 'N/A') . "\n\n";

// Get a sample product with department
$product = \Modules\CatalogManagement\app\Models\Product::with(['department', 'category', 'subCategory'])
    ->whereNotNull('department_id')
    ->first();

if (!$product) {
    echo "No products found with department!\n";
    exit;
}

echo "Using Product ID: {$product->id}\n";
echo "Product Name: {$product->name}\n";
echo "Department ID: {$product->department_id}\n";
echo "Department Name: " . ($product->department->name ?? 'N/A') . "\n";
echo "Category ID: {$product->category_id}\n";
echo "Category Name: " . ($product->category->name ?? 'N/A') . "\n\n";

// Prepare cart items (system uses departments)
$cartItems = [
    [
        'type' => 'department',
        'type_id' => $product->department_id,
        'type_name' => $product->department->name ?? null,
        'product_id' => $product->id,
        'vendor_id' => $product->vendor_id ?? null,
        'quantity' => 1,
    ]
];

echo "=== CALLING SHIPPING CALCULATION SERVICE ===\n";

try {
    $service = app(\Modules\Order\app\Services\ShippingCalculationService::class);
    $result = $service->calculateShipping(
        $customer->id,
        $address->id,
        $cartItems
    );
    
    echo "SUCCESS!\n\n";
    echo "Shipping Cost: {$result['shipping_cost']}\n";
    echo "Breakdown:\n";
    print_r($result['breakdown']);
    echo "\nProduct Shipping:\n";
    print_r($result['product_shipping']);
    echo "\nAddress Info:\n";
    print_r($result['address']);
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
