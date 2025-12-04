<?php

namespace Modules\Order\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderProduct;
use Modules\Customer\app\Models\Customer;
use Modules\Vendor\app\Models\Vendor;
use Modules\CatalogManagement\app\Models\VendorProduct;

class OrderDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Order Database Seeder...');

        // Check if we have required data
        $vendorCount = Vendor::count();
        $vendorProductCount = VendorProduct::count();

        if ($vendorCount === 0) {
            $this->command->error('No vendors found! Please run vendor seeder first.');
            return;
        }

        if ($vendorProductCount === 0) {
            $this->command->error('No vendor products found! Please run product seeder first.');
            return;
        }

        // Create customers if they don't exist
        $customerCount = Customer::count();
        if ($customerCount < 50) {
            $this->command->info('Creating customers...');
            Customer::factory(100)->create();
        }

        $this->command->info("Creating 1000 orders with products...");

        // Create orders in batches for better performance
        $batchSize = 100;
        $totalOrders = 1000;

        for ($i = 0; $i < $totalOrders; $i += $batchSize) {
            $currentBatch = min($batchSize, $totalOrders - $i);

            Order::factory($currentBatch)->create()->each(function ($order) {
                $productsCount = rand(1, 5);
                $totalProductPrice = 0;
                $totalItemsCount = 0;

                // Create order products
                for ($j = 0; $j < $productsCount; $j++) {
                    $orderProduct = OrderProduct::factory()->create([
                        'order_id' => $order->id,
                    ]);

                    $totalProductPrice += $orderProduct->price * $orderProduct->quantity;
                    $totalItemsCount += $orderProduct->quantity;
                }

                // Update order totals based on actual products
                $shipping = $order->shipping;
                $totalTax = $totalProductPrice * 0.15; // 15% tax
                $promoDiscount = $order->customer_promo_code_value ?? 0;
                $totalPrice = $totalProductPrice + $shipping + $totalTax - $promoDiscount;

                $order->update([
                    'total_product_price' => $totalProductPrice,
                    'total_tax' => $totalTax,
                    'total_price' => $totalPrice,
                    'items_count' => $totalItemsCount,
                ]);
            });

            $this->command->info("Created " . ($i + $currentBatch) . "/$totalOrders orders...");
        }

        $this->command->info('Order Database Seeder completed successfully!');
    }
}
