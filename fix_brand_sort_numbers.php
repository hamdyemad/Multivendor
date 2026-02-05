<?php

/**
 * Script to fix brand sort numbers
 * Sets incremental sort numbers for all brands that have sort_number = 0
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\CatalogManagement\app\Models\Brand;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();
    
    echo "Starting to fix brand sort numbers...\n\n";
    
    // Get all brands ordered by created_at (oldest first)
    $brands = Brand::orderBy('created_at', 'asc')->get();
    
    echo "Found " . $brands->count() . " brand(s)\n\n";
    
    $sortNumber = 1;
    $updated = 0;
    
    foreach ($brands as $brand) {
        $oldSortNumber = $brand->sort_number;
        
        // Update sort number
        $brand->update(['sort_number' => $sortNumber]);
        
        echo "Brand ID {$brand->id}: {$oldSortNumber} → {$sortNumber}\n";
        
        $sortNumber++;
        $updated++;
    }
    
    DB::commit();
    
    echo "\n✅ Successfully updated {$updated} brand(s)\n";
    echo "Sort numbers now range from 1 to {$updated}\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
