<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed users types
        // $this->call(UserTypeSeeder::class);

        // $this->call(UserSeeder::class);

        // // Seed languages first (required for translations)
        // $this->call(LanguageSeeder::class);

        // // Seed permissions with translations
        // $this->call(PermessionSeeder::class);

        // // Seed roles with permissions
        // $this->call(RoleSeeder::class);

        // Orders Seeder to implement withdraw cycle
        // $this->call(OrderDatabaseSeeder::class);

        // $this->call(ActivitySeeder::class);

        // Seed vendors with country_id
        // $this->call(VendorSeeder::class);

        // Seed brands with country_id
        // $this->call(BrandSeeder::class);

        // Auto-generate products for all vendors (40 products each)
        // $this->call(AutoProductSeeder::class);

        // $this->call(OrderStageSeeder::class);

        $this->call(SyncVendorUsersSeeder::class);
    }
}
