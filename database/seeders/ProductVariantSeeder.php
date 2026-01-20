<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\CatalogManagement\app\Models\Product;
use Modules\CatalogManagement\app\Models\ProductVariant;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates ProductVariant records for products that have VendorProductVariants
     * but are missing corresponding ProductVariant records.
     */
    public function run(): void
    {
        $this->command->info('Starting ProductVariant seeder...');
        
        $totalCreated = 0;
        $totalSkipped = 0;
        $totalErrors = 0;

        // Get all products with configuration_type = 'variants'
        $products = Product::where('configuration_type', 'variants')->get();
        
        $this->command->info("Found {$products->count()} products with variants configuration");

        foreach ($products as $product) {
            try {
                // Get all vendor products for this product
                $vendorProducts = VendorProduct::where('product_id', $product->id)
                    ->with(['variants'])
                    ->get();

                if ($vendorProducts->isEmpty()) {
                    $this->command->warn("Product ID {$product->id} has no vendor products");
                    continue;
                }

                // Collect all unique variant configurations from all vendors
                $variantConfigurations = [];
                
                foreach ($vendorProducts as $vendorProduct) {
                    foreach ($vendorProduct->variants as $vendorVariant) {
                        if ($vendorVariant->variant_configuration_id) {
                            $variantConfigurations[$vendorVariant->variant_configuration_id] = [
                                'variant_configuration_id' => $vendorVariant->variant_configuration_id,
                                'sku' => $vendorVariant->sku,
                            ];
                        }
                    }
                }

                if (empty($variantConfigurations)) {
                    $this->command->warn("Product ID {$product->id} has no variant configurations");
                    $totalSkipped++;
                    continue;
                }

                // Create ProductVariant records if they don't exist
                $createdForProduct = 0;
                $skippedForProduct = 0;

                foreach ($variantConfigurations as $configId => $variantData) {
                    // Check if ProductVariant already exists
                    $existingVariant = ProductVariant::where('product_id', $product->id)
                        ->where('variant_configuration_id', $configId)
                        ->first();

                    if ($existingVariant) {
                        $skippedForProduct++;
                        continue;
                    }

                    // Create new ProductVariant (without SKU - SKU is only in vendor_product_variants)
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'variant_configuration_id' => $configId,
                    ]);

                    $createdForProduct++;
                    $totalCreated++;
                }

                if ($createdForProduct > 0) {
                    $this->command->info("Product ID {$product->id}: Created {$createdForProduct} variants, Skipped {$skippedForProduct}");
                } else {
                    $totalSkipped++;
                }

            } catch (\Exception $e) {
                $totalErrors++;
                $this->command->error("Error processing Product ID {$product->id}: " . $e->getMessage());
                Log::error('ProductVariantSeeder error', [
                    'product_id' => $product->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->command->info('');
        $this->command->info('=== ProductVariant Seeder Summary ===');
        $this->command->info("Total products processed: {$products->count()}");
        $this->command->info("Total variants created: {$totalCreated}");
        $this->command->info("Total products skipped (already had variants): {$totalSkipped}");
        $this->command->info("Total errors: {$totalErrors}");
        $this->command->info('=====================================');
        
        Log::info('ProductVariantSeeder completed', [
            'total_products' => $products->count(),
            'total_created' => $totalCreated,
            'total_skipped' => $totalSkipped,
            'total_errors' => $totalErrors,
        ]);
    }
}
