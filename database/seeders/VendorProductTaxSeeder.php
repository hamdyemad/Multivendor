<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\Tax;

class VendorProductTaxSeeder extends Seeder
{
    /**
     * Assign all active taxes to every vendor product.
     */
    public function run(): void
    {
        $this->command->info('Starting VendorProductTax seeder...');

        // Get all vendor products (country filtered by trait)
        $vendorProducts = VendorProduct::all();
        
        // Get all active taxes (country filtered by trait)
        $taxes = Tax::where('is_active', true)->get();

        if ($vendorProducts->isEmpty()) {
            $this->command->warn('No vendor products found.');
            return;
        }

        if ($taxes->isEmpty()) {
            $this->command->warn('No active taxes found.');
            return;
        }

        $taxIds = $taxes->pluck('id')->toArray();
        $totalAssigned = 0;

        foreach ($vendorProducts as $vendorProduct) {
            // Sync all taxes to this vendor product (won't duplicate existing)
            $vendorProduct->taxes()->syncWithoutDetaching($taxIds);
            $totalAssigned++;
        }

        $this->command->info("Assigned " . count($taxIds) . " taxes to {$totalAssigned} vendor products.");
    }
}
