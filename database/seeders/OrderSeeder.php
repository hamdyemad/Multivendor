<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderProduct;
use Modules\Order\app\Models\OrderStage;
use Modules\Customer\app\Models\Customer;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\AreaSettings\app\Models\Country;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Region;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing orders
        Order::query()->forceDelete();
        OrderProduct::query()->forceDelete();

        // Get required data
        $customers = Customer::all();
        $stages = OrderStage::all();
        $vendorProducts = VendorProduct::with('product.department.activities')->limit(20)->get();
        $countries = Country::where('active', true)->limit(5)->get();

        if ($customers->isEmpty()) {
            $this->command->error('No customers found. Please run CustomerSeeder first.');
            return;
        }

        if ($stages->isEmpty()) {
            $this->command->error('No order stages found. Please run OrderStageSeeder first.');
            return;
        }

        if ($vendorProducts->isEmpty()) {
            $this->command->error('No vendor products found. Please create products first.');
            return;
        }

        if ($countries->isEmpty()) {
            $this->command->error('No countries found. Please seed countries first.');
            return;
        }

        $orderCount = 0;

        // Create 30 orders
        for ($i = 1; $i <= 30; $i++) {
            try {
                $customer = $customers->random();
                $stage = $stages->random();
                // Use customer's country
                $country = Country::find($customer->country_id);

                if (!$country) {
                    $this->command->warning("Customer {$customer->id} has no country_id. Skipping order creation.");
                    continue;
                }

                $city = $country->cities()->first();
                $region = $city ? $city->regions()->first() : null;

                // Generate order number
                $orderNumber = 'ORD-' . date('Y') . '-' . str_pad($i, 5, '0', STR_PAD_LEFT);

                // Random order data
                $itemsCount = rand(1, 5);
                $totalProductPrice = 0;
                $totalTax = 0;
                $shipping = rand(50, 200);
                $promoDiscount = rand(0, 1) ? rand(10, 100) : 0;

                // Create order
                $order = Order::create([
                    'order_number' => $orderNumber,
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->full_name,
                    'customer_email' => $customer->email,
                    'customer_phone' => $customer->phone,
                    'customer_address' => $this->generateAddress(),
                    'order_from' => 'web',
                    'payment_type' => $this->getRandomPaymentType(),
                    'customer_promo_code_title' => $promoDiscount > 0 ? 'PROMO' . rand(100, 999) : null,
                    'customer_promo_code_value' => $promoDiscount,
                    'customer_promo_code_type' => $promoDiscount > 0 ? 'fixed' : null,
                    'shipping' => $shipping,
                    'stage_id' => $stage->id,
                    'country_id' => $country->id,
                    'city_id' => $city ? $city->id : null,
                    'region_id' => $region ? $region->id : null,
                    'items_count' => $itemsCount,
                ]);

                // Add order products
                $selectedProducts = $vendorProducts->random(min($itemsCount, $vendorProducts->count()));

                $commission = 0;
                foreach ($selectedProducts as $vendorProduct) {

                    $activies = $vendorProduct->product->department->activities;
                    foreach($activies as $activity) {
                        $commission+= $activity->commission;
                    }

                    $tax_rate = $vendorProduct->tax->tax_rate;
                    $quantity = rand(1, 3);
                    $price = rand(100, 5000);
                    $productTotal = $price * $quantity;
                    $tax = round($productTotal * $tax_rate, 2); // 5% tax

                    OrderProduct::create([
                        'order_id' => $order->id,
                        'vendor_id' => $vendorProduct->vendor_id,
                        'vendor_product_id' => $vendorProduct->id,
                        'vendor_product_variant_id' => $vendorProduct->variants()->first()?->id,
                        'price' => $price,
                        'quantity' => $quantity,
                        'commission' => $commission,
                    ]);

                    $totalProductPrice += $productTotal;
                    $totalTax += $tax;
                }

                // Calculate total price
                $totalPrice = $totalProductPrice + $shipping + $totalTax - $promoDiscount;

                // Update order with calculated totals
                $order->update([
                    'total_product_price' => $totalProductPrice,
                    'total_tax' => $totalTax,
                    'total_price' => max(0, $totalPrice), // Ensure no negative totals
                ]);

                $orderCount++;
                $this->command->info("✓ Created order: {$orderNumber} (ID: {$order->id}) - Customer: {$customer->full_name}");

            } catch (\Exception $e) {
                $this->command->error("✗ Failed to create order #{$i}: {$e->getMessage()}");
            }
        }

        $this->command->info("Orders seeded successfully! Total orders created: {$orderCount}");
    }

    /**
     * Generate a random address
     */
    private function generateAddress(): string
    {
        $streets = ['Main Street', 'Oak Avenue', 'Elm Road', 'Pine Lane', 'Maple Drive', 'Cedar Street', 'Birch Road'];
        $cities = ['Cairo', 'Alexandria', 'Giza', 'Helwan', 'Zagazig'];
        $buildingNumbers = rand(1, 500);
        $apartmentNumbers = rand(1, 20);

        $street = $streets[array_rand($streets)];
        $city = $cities[array_rand($cities)];

        return "{$buildingNumbers} {$street}, Apt {$apartmentNumbers}, {$city}";
    }

    /**
     * Get random payment type
     */
    private function getRandomPaymentType(): string
    {
        $paymentTypes = ['credit_card', 'debit_card', 'cash_on_delivery', 'bank_transfer', 'wallet'];
        return $paymentTypes[array_rand($paymentTypes)];
    }
}
