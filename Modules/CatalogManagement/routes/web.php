<?php

use Illuminate\Support\Facades\Route;
use Modules\CatalogManagement\app\Http\Controllers\BrandController;
use Modules\CatalogManagement\app\Http\Controllers\TaxController;
use Modules\CatalogManagement\app\Http\Controllers\ProductController;

Route::group(
[
    'prefix' => 'admin',
    'as' => 'admin.'
], function(){
    // Brands
    Route::get('brands/datatable', [BrandController::class, 'datatable'])->name('brands.datatable');
    Route::get('brands/search', [BrandController::class, 'brandSearch'])->name('brands.search');
    Route::resource('brands', BrandController::class);
    
    // Taxes
    Route::get('taxes/datatable', [TaxController::class, 'datatable'])->name('taxes.datatable');
    Route::get('taxes/search', [TaxController::class, 'taxSearch'])->name('taxes.search');
    Route::resource('taxes', TaxController::class);
    
    // Products
    Route::get('products/datatable', [ProductController::class, 'datatable'])->name('products.datatable');
    Route::get('categories/by-department', [ProductController::class, 'getCategoriesByDepartment'])->name('categories.by-department');
    Route::get('subcategories/by-category', [ProductController::class, 'getSubCategoriesByCategory'])->name('subcategories.by-category');
    Route::resource('products', ProductController::class);
});
