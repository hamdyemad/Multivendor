<?php

namespace Modules\Order\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Order\app\Models\OrderProduct;
use Modules\Vendor\app\Models\Vendor;

class OrderProductFactory extends Factory
{
    protected $model = OrderProduct::class;

    public function definition(): array
    {
        $vendor = Vendor::inRandomOrder()->first() ;
        return [
            'vendor_id' => $vendor->id ?? 1,
            'price' => $this->faker->randomFloat(2, 50, 1000),
            'commission' => $vendor->commission->commission,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
