<?php

namespace Modules\Customer\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Customer\app\Models\Customer;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'password' => 'password123', // Will be hashed by mutator
            'status' => $this->faker->boolean(90), // 90% active
            'lang' => $this->faker->randomElement(['en', 'ar']),
            'email_verified_at' => $this->faker->boolean(80) ? now() : null,
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'updated_at' => now(),
        ];
    }
}
