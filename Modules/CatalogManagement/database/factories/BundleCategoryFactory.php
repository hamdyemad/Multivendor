<?php

namespace Modules\CatalogManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\CatalogManagement\app\Models\BundleCategory;
use Illuminate\Support\Str;

class BundleCategoryFactory extends Factory
{
    protected $model = BundleCategory::class;

    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->slug(),
            'active' => 1,
            'country_id' => 1,
        ];
    }

    /**
     * Indicate that the bundle category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => 0,
        ]);
    }

    /**
     * Configure the model factory with translations.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (BundleCategory $category) {
            // Create English translation
            $category->translations()->create([
                'lang_id' => 1,
                'lang_key' => 'name',
                'lang_value' => $this->faker->words(3, true),
            ]);

            // Create Arabic translation
            $category->translations()->create([
                'lang_id' => 2,
                'lang_key' => 'name',
                'lang_value' => $this->faker->words(3, true),
            ]);
        });
    }
}
