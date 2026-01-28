<?php

namespace Modules\CatalogManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\CatalogManagement\app\Models\Bundle;
use Modules\CatalogManagement\app\Models\BundleCategory;

class BundleFactory extends Factory
{
    protected $model = Bundle::class;

    public function definition(): array
    {
        return [
            'bundle_category_id' => BundleCategory::factory(),
            'sku' => $this->faker->unique()->numerify('BUN-####'),
            'slug' => $this->faker->unique()->slug(),
            'is_active' => true,
            'admin_approval' => 1,
            'country_id' => 1,
        ];
    }

    /**
     * Indicate that the bundle is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the bundle is pending approval.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_approval' => 0,
        ]);
    }

    /**
     * Indicate that the bundle is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_approval' => 2,
        ]);
    }

    /**
     * Indicate that the bundle is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_approval' => 1,
        ]);
    }
}
