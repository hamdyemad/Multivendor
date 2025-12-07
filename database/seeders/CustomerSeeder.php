<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Customer\app\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\AreaSettings\app\Models\Country;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing customers
        Customer::withTrashed()->forceDelete();

        // Get country_id from current country_code in session
        $countryCode = session('country_code', 'EG');
        $country = Country::where('code', strtoupper($countryCode))->first();

        if (!$country) {
            $country = Country::first();
        }

        $countryId = $country ? $country->id : null;


        $customers = [
            [
                'country_id' => $countryId,
                'first_name' => 'Ahmed',
                'last_name' => 'Hassan',
                'email' => 'ahmed.hassan@example.com',
                'phone' => '+201001234567',
                'password' => Hash::make('password123'),
                'status' => true,
                'email_verified_at' => now(),
            ],
            [
                'country_id' => $countryId,
                'first_name' => 'Fatima',
                'last_name' => 'Ali',
                'email' => 'fatima.ali@example.com',
                'phone' => '+201112345678',
                'password' => Hash::make('password123'),
                'status' => true,
                'email_verified_at' => now(),
            ],
            [
                'country_id' => $countryId,
                'first_name' => 'Mohammed',
                'last_name' => 'Ibrahim',
                'email' => 'mohammed.ibrahim@example.com',
                'phone' => '+201223456789',
                'password' => Hash::make('password123'),
                'status' => true,
                'email_verified_at' => now(),
            ],
            [
                'country_id' => $countryId,
                'first_name' => 'Layla',
                'last_name' => 'Mohamed',
                'email' => 'layla.mohamed@example.com',
                'phone' => '+201334567890',
                'password' => Hash::make('password123'),
                'status' => true,
                'email_verified_at' => now(),
            ],
            [
                'country_id' => $countryId,
                'first_name' => 'Omar',
                'last_name' => 'Khalil',
                'email' => 'omar.khalil@example.com',
                'phone' => '+201445678901',
                'password' => Hash::make('password123'),
                'status' => true,
                'email_verified_at' => now(),
            ],
            [
                'country_id' => $countryId,
                'first_name' => 'Noor',
                'last_name' => 'Karim',
                'email' => 'noor.karim@example.com',
                'phone' => '+201556789012',
                'password' => Hash::make('password123'),
                'status' => true,
                'email_verified_at' => now(),
            ],
            [
                'country_id' => $countryId,
                'first_name' => 'Hana',
                'last_name' => 'Rashid',
                'email' => 'hana.rashid@example.com',
                'phone' => '+201667890123',
                'password' => Hash::make('password123'),
                'status' => true,
                'email_verified_at' => now(),
            ],
            [
                'country_id' => $countryId,
                'first_name' => 'Karim',
                'last_name' => 'Samir',
                'email' => 'karim.samir@example.com',
                'phone' => '+201778901234',
                'password' => Hash::make('password123'),
                'status' => true,
                'email_verified_at' => now(),
            ],
            [
                'country_id' => $countryId,
                'first_name' => 'Dina',
                'last_name' => 'Youssef',
                'email' => 'dina.youssef@example.com',
                'phone' => '+201889012345',
                'password' => Hash::make('password123'),
                'status' => true,
                'email_verified_at' => now(),
            ],
            [
                'country_id' => $countryId,
                'first_name' => 'Zain',
                'last_name' => 'Nasser',
                'email' => 'zain.nasser@example.com',
                'phone' => '+201990123456',
                'password' => Hash::make('password123'),
                'status' => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($customers as $customerData) {
            try {
                $customer = Customer::create($customerData);
                $this->command->info("✓ Created customer: {$customer->full_name} (ID: {$customer->id})");
            } catch (\Exception $e) {
                $this->command->error("✗ Failed to create customer {$customerData['first_name']}: {$e->getMessage()}");
            }
        }

        $this->command->info('Customers seeded successfully!');
    }
}
