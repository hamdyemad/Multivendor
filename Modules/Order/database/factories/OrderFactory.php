<?php

namespace Modules\Order\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderStage;
use Modules\Customer\app\Models\Customer;
use Modules\AreaSettings\app\Models\Country;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Region;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        // Get random existing records or use defaults
        $country = Country::inRandomOrder()->first();
        $city = $country ? $country->cities()->inRandomOrder()->first() : null;
        $region = $city ? $city->regions()->inRandomOrder()->first() : null;
        $stage = OrderStage::inRandomOrder()->first();
        $customer = Customer::inRandomOrder()->first();

        $totalProductPrice = $this->faker->randomFloat(2, 50, 1000);
        $shipping = $this->faker->randomFloat(2, 5, 50);
        $totalTax = $totalProductPrice * 0.15; // 15% tax
        $promoDiscount = $this->faker->boolean(30) ? $this->faker->randomFloat(2, 5, 50) : 0;
        $totalPrice = $totalProductPrice + $shipping + $totalTax - $promoDiscount;

        return [
            'order_number' => 'ORD-' . $this->faker->unique()->numerify('########'),
            'customer_id' => $customer?->id,
            'customer_name' => $customer ? ($customer->first_name . ' ' . $customer->last_name) : $this->faker->name(),
            'customer_email' => $customer?->email ?? $this->faker->email(),
            'customer_address' => $this->faker->address(),
            'customer_phone' => $this->faker->phoneNumber(),
            'order_from' => $this->faker->randomElement(['ios', 'android', 'web']),
            'payment_type' => $this->faker->randomElement(['cash_on_delivery', 'online']),
            'customer_promo_code_title' => $this->faker->boolean(30) ? $this->faker->word() : null,
            'customer_promo_code_value' => $promoDiscount > 0 ? $promoDiscount : null,
            'customer_promo_code_type' => $promoDiscount > 0 ? $this->faker->randomElement(['percentage', 'fixed']) : null,
            'shipping' => $shipping,
            'total_tax' => $totalTax,
            'total_product_price' => $totalProductPrice,
            'items_count' => $this->faker->numberBetween(1, 5),
            'total_price' => $totalPrice,
            'stage_id' => $stage?->id,
            'country_id' => $country?->id,
            'city_id' => $city?->id,
            'region_id' => $region?->id,
            'refunded_amount' => $this->faker->boolean(10) ? $this->faker->randomFloat(2, 0, $totalPrice * 0.5) : 0,
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => now(),
        ];
    }
}
