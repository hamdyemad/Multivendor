<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AutoProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * NOTE: This seeder is disabled. Products should be created via:
     * 1. Injection API (/api/inject-data)
     * 2. Manual creation in admin panel
     * 
     * All products will be assigned to Bnaia vendor automatically.
     */
    public function run(): void
    {
        echo "\n🚀 Auto Product Seeder - SKIPPED\n";
        echo "   ℹ️  Products should be created via injection API or manually.\n";
        echo "   ℹ️  This seeder is disabled to prevent duplicate products.\n";
        echo "   ℹ️  All products will be assigned to Bnaia vendor.\n\n";
    }
}
