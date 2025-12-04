<?php

namespace Modules\Order\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Order\app\Models\OrderProduct;
use Modules\Vendor\app\Models\Vendor;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;

class OrderProductFactory extends Factory
{
    protected $model = OrderProduct::class;

    public function definition(): array
    {
        $vendor = Vendor::inRandomOrder()->first();
        $vendorProduct = VendorProduct::where('vendor_id', $vendor?->id)->inRandomOrder()->first();
        $vendorProductVariant = $vendorProduct ? $vendorProduct->variants()->inRandomOrder()->first() : null;

        $price = $this->faker->randomFloat(2, 50, 1000);
        $commission = $price * 0.1; // 10% commission
        $quantity = $this->faker->numberBetween(1, 5);

        return [
            'vendor_id' => $vendor?->id ?? 1,
            'price' => $price,
            'commission' => $commission,
            'vendor_product_id' => $vendorProduct?->id,
            'vendor_product_variant_id' => $vendorProductVariant?->id,
            'quantity' => $quantity,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
