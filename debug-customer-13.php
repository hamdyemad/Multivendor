<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CUSTOMER 13 DEBUG ===\n\n";

// Get customer with addresses
$customer = \Modules\Customer\app\Models\Customer::with(['addresses.city'])->find(13);

if (!$customer) {
    echo "Customer 13 not found!\n";
    exit;
}

echo "Customer ID: {$customer->id}\n";
echo "Customer Name: {$customer->name}\n";
echo "Customer Email: {$customer->email}\n\n";

echo "=== ADDRESSES ===\n";
foreach ($customer->addresses as $addr) {
    echo "Address ID: {$addr->id}\n";
    echo "  Title: {$addr->title}\n";
    echo "  City ID: " . ($addr->city_id ?? 'NULL') . "\n";
    echo "  City Name: " . ($addr->city->name ?? 'N/A') . "\n";
    echo "  Is Primary: " . ($addr->is_primary ? 'Yes' : 'No') . "\n";
    echo "  ---\n";
}

// Check if there are active shippings for the customer's city
if ($customer->addresses->isNotEmpty()) {
    $firstAddress = $customer->addresses->first();
    $cityId = $firstAddress->city_id;
    
    echo "\n=== SHIPPING RULES FOR CITY {$cityId} ===\n";
    
    $shippings = \Modules\Order\app\Models\Shipping::withoutGlobalScope('country_filter')
        ->where('active', 1)
        ->whereHas('cities', function($q) use ($cityId) {
            $q->withoutGlobalScope('country_filter')
              ->where('cities.id', $cityId);
        })
        ->with(['cities', 'categories', 'departments', 'subCategories'])
        ->get();
    
    echo "Active shippings for this city: " . $shippings->count() . "\n\n";
    
    foreach ($shippings as $shipping) {
        echo "Shipping ID: {$shipping->id}\n";
        echo "  Name: {$shipping->name}\n";
        echo "  Cost: {$shipping->cost}\n";
        echo "  Cities: " . $shipping->cities->pluck('name')->implode(', ') . "\n";
        echo "  Departments: " . $shipping->departments->pluck('name')->implode(', ') . "\n";
        echo "  Categories: " . $shipping->categories->pluck('name')->implode(', ') . "\n";
        echo "  Sub Categories: " . $shipping->subCategories->pluck('name')->implode(', ') . "\n";
        echo "  ---\n";
    }
}

// Check system shipping settings
echo "\n=== SYSTEM SHIPPING SETTINGS ===\n";
$settings = \Modules\SystemSetting\app\Models\SiteInformation::first();
echo "shipping_allow_departments: " . ($settings->shipping_allow_departments ?? 'null') . "\n";
echo "shipping_allow_categories: " . ($settings->shipping_allow_categories ?? 'null') . "\n";
echo "shipping_allow_sub_categories: " . ($settings->shipping_allow_sub_categories ?? 'null') . "\n";
