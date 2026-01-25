<?php
/**
 * Script to fix Product 346 incorrect image
 * 
 * Run this from the Laravel root directory:
 * php fix_product_346_image.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\CatalogManagement\app\Models\Product;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

// Find the product
$vendorProduct = \Modules\CatalogManagement\app\Models\VendorProduct::find(346);

if (!$vendorProduct) {
    echo "Product 346 not found!\n";
    exit(1);
}

$product = $vendorProduct->product;

if (!$product) {
    echo "Base product not found for vendor product 346!\n";
    exit(1);
}

echo "Found product: {$product->id}\n";
echo "Product title: " . ($product->title ?? 'N/A') . "\n";

// Get the main image
$mainImage = $product->mainImage;

if ($mainImage) {
    echo "\nCurrent main image:\n";
    echo "  ID: {$mainImage->id}\n";
    echo "  Path: {$mainImage->path}\n";
    
    // Ask for confirmation
    echo "\nDo you want to delete this image? (yes/no): ";
    $handle = fopen ("php://stdin","r");
    $line = fgets($handle);
    $answer = trim(strtolower($line));
    
    if($answer == 'yes' || $answer == 'y'){
        // Delete the physical file
        if (Storage::disk('public')->exists($mainImage->path)) {
            Storage::disk('public')->delete($mainImage->path);
            echo "✓ Physical file deleted\n";
        } else {
            echo "⚠ Physical file not found\n";
        }
        
        // Delete the database record
        $mainImage->delete();
        echo "✓ Database record deleted\n";
        echo "\nImage removed successfully!\n";
        echo "Please upload a new product image via the admin panel.\n";
    } else {
        echo "Operation cancelled.\n";
    }
    
    fclose($handle);
} else {
    echo "\nNo main image found for this product.\n";
}

// Check for additional images
$additionalImages = Attachment::where('attachable_id', $product->id)
    ->where('attachable_type', Product::class)
    ->where('type', 'additional_image')
    ->get();

if ($additionalImages->count() > 0) {
    echo "\nAdditional images found: {$additionalImages->count()}\n";
    foreach ($additionalImages as $img) {
        echo "  - ID: {$img->id}, Path: {$img->path}\n";
    }
}

echo "\nDone!\n";
