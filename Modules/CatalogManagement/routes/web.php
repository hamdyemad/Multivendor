<?php

use Illuminate\Support\Facades\Route;
use Modules\CatalogManagement\app\Http\Controllers\BrandController;
use Modules\CatalogManagement\app\Http\Controllers\TaxController;
use Modules\CatalogManagement\app\Http\Controllers\ProductController;
use Modules\CatalogManagement\app\Http\Controllers\VariantConfigurationKeyController;
use Modules\CatalogManagement\app\Http\Controllers\VariantsConfigurationController;

Route::group(
[
    'prefix' => 'admin',
    'as' => 'admin.'
], function(){
    // Brands
    Route::get('brands/datatable', [BrandController::class, 'datatable'])->name('brands.datatable');
    Route::resource('brands', BrandController::class);
    
    // Taxes
    Route::get('taxes/datatable', [TaxController::class, 'datatable'])->name('taxes.datatable');
    Route::resource('taxes', TaxController::class);
    
    // Products
    Route::get('products/datatable', [ProductController::class, 'datatable'])->name('products.datatable');
    Route::resource('products', ProductController::class);
    
    // Variant Configuration Keys
    Route::get('variant-keys/datatable', [VariantConfigurationKeyController::class, 'datatable'])->name('variant-keys.datatable');
    Route::get('variant-keys-tree', [VariantConfigurationKeyController::class, 'tree'])->name('variant-keys.tree');
    Route::resource('variant-keys', VariantConfigurationKeyController::class);
    
    // Variants Configurations
    Route::get('variants-configurations/datatable', [VariantsConfigurationController::class, 'datatable'])->name('variants-configurations.datatable');
    Route::get('variants-configurations-tree', [VariantsConfigurationController::class, 'tree'])->name('variants-configurations.tree');
    Route::resource('variants-configurations', VariantsConfigurationController::class);
});
