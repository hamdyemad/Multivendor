<?php

namespace Modules\CatalogManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\CatalogManagement\app\Models\Review;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\Customer\app\Models\Customer;
use Modules\Vendor\app\Models\Vendor;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderProduct;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Review Seeder...');

        // Check if we have required data
        $customerCount = Customer::count();
        $vendorProductCount = VendorProduct::count();
        $vendorCount = Vendor::count();
        $orderCount = Order::count();

        if ($customerCount === 0) {
            $this->command->error('No customers found! Please run customer seeder first.');
            return;
        }

        if ($vendorProductCount === 0 && $vendorCount === 0) {
            $this->command->error('No vendor products or vendors found! Please run product seeder first.');
            return;
        }

        $this->command->info("Found {$customerCount} customers, {$vendorProductCount} products, {$vendorCount} vendors");

        // Get customers who have orders (more realistic - they can review products they bought)
        $customerIdsWithOrders = Order::whereNotNull('customer_id')->pluck('customer_id')->unique();
        $customersWithOrders = Customer::whereIn('id', $customerIdsWithOrders)->get();

        if ($customersWithOrders->isEmpty()) {
            $this->command->warn('No customers with orders found. Using all customers for reviews.');
            $customersWithOrders = Customer::all();
        }

        $totalReviews = 500;
        $createdReviews = 0;

        $this->command->info("Creating {$totalReviews} reviews...");

        // Create reviews based on orders (customers reviewing products they bought)
        $orders = Order::whereNotNull('customer_id')
            ->with(['products.vendorProduct'])
            ->inRandomOrder()
            ->limit(300)
            ->get();

        foreach ($orders as $order) {
            foreach ($order->products as $orderProduct) {
                if (!$orderProduct->vendor_product_id) {
                    continue;
                }

                // Check if customer already reviewed this product
                $existingReview = Review::where('customer_id', $order->customer_id)
                    ->where('reviewable_type', VendorProduct::class)
                    ->where('reviewable_id', $orderProduct->vendor_product_id)
                    ->exists();

                if ($existingReview) {
                    continue;
                }

                try {
                    Review::factory()->create([
                        'customer_id' => $order->customer_id,
                        'reviewable_type' => VendorProduct::class,
                        'reviewable_id' => $orderProduct->vendor_product_id,
                        'created_at' => $order->created_at ? \Carbon\Carbon::parse($order->getRawOriginal('created_at'))->addDays(rand(1, 14)) : now(),
                    ]);
                    $createdReviews++;
                } catch (\Exception $e) {
                    // Skip if there's a constraint error
                    continue;
                }

                if ($createdReviews % 100 === 0) {
                    $this->command->info("Created {$createdReviews} product reviews...");
                }

                if ($createdReviews >= $totalReviews) {
                    break 2;
                }
            }
        }

        // If we need more reviews, create some vendor reviews
        $remainingReviews = $totalReviews - $createdReviews;
        if ($remainingReviews > 0 && $vendorCount > 0) {
            $this->command->info("Creating {$remainingReviews} vendor reviews...");

            $vendors = Vendor::all();
            $customers = Customer::inRandomOrder()->limit($remainingReviews)->get();

            foreach ($customers as $customer) {
                $vendor = $vendors->random();

                // Check if customer already reviewed this vendor
                $existingReview = Review::where('customer_id', $customer->id)
                    ->where('reviewable_type', Vendor::class)
                    ->where('reviewable_id', $vendor->id)
                    ->exists();

                if ($existingReview) {
                    continue;
                }

                try {
                    Review::factory()->create([
                        'customer_id' => $customer->id,
                        'reviewable_type' => Vendor::class,
                        'reviewable_id' => $vendor->id,
                    ]);
                    $createdReviews++;
                } catch (\Exception $e) {
                    continue;
                }

                if ($createdReviews >= $totalReviews) {
                    break;
                }
            }
        }

        $this->command->info("Review Seeder completed! Created {$createdReviews} reviews.");
    }
}
