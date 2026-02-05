<?php

use Illuminate\Support\Facades\Route;

// System Catalog - Accessible to all authenticated users (admins and vendors)
Route::get('system-catalog', 'SystemCatalogController@index')->name('system-catalog.index');
Route::get('system-catalog/departments', 'SystemCatalogController@departments')->name('system-catalog.departments');
Route::get('system-catalog/categories', 'SystemCatalogController@categories')->name('system-catalog.categories');
Route::get('system-catalog/variants', 'SystemCatalogController@variants')->name('system-catalog.variants');
Route::get('system-catalog/brands', 'SystemCatalogController@brands')->name('system-catalog.brands');
Route::get('system-catalog/regions', 'SystemCatalogController@regions')->name('system-catalog.regions');
Route::get('system-catalog/vendors', 'SystemCatalogController@vendors')->name('system-catalog.vendors');

// Products Export - Accessible to all authenticated users (admins and vendors)
Route::get('products/export', 'ProductController@export')->name('products.export');

Route::group(['middleware' => 'adminGuard'], function() {
    // Reviews
    Route::get('reviews', 'ReviewController@index')->name('reviews.index');
    Route::get('reviews/datatable', 'ReviewController@datatable')->name('reviews.datatable');
    Route::post('reviews/{review}/approve', 'ReviewController@approve')->name('reviews.approve');
    Route::post('reviews/{review}/reject', 'ReviewController@reject')->name('reviews.reject');

    // Brands
    Route::get('brands/datatable', 'BrandController@datatable')->name('brands.datatable');
    Route::post('brands/reorder', 'BrandController@reorder')->name('brands.reorder');
    Route::resource('brands', 'BrandController');

    // Promocodes
    Route::get('promocodes/datatable', 'PromocodeController@datatable')->name('promocodes.datatable');
    Route::post('promocodes/{promocode}/change-status', 'PromocodeController@changeStatus')->name('promocodes.change-status');
    Route::resource('promocodes', 'PromocodeController');

    // Taxes
    Route::get('taxes/datatable', 'TaxController@datatable')->name('taxes.datatable');
    Route::post('taxes/{id}/toggle-status', 'TaxController@toggleStatus')->name('taxes.toggle-status');
    Route::resource('taxes', 'TaxController');

    // Bundle Categories
    Route::get('bundle-categories/datatable', 'BundleCategoryController@datatable')->name('bundle-categories.datatable');
    Route::post('bundle-categories/{id}/toggle-status', 'BundleCategoryController@toggleStatus')->name('bundle-categories.toggle-status');
    Route::resource('bundle-categories', 'BundleCategoryController');

});

// Variant Configuration Keys (accessible by both admin and vendor - permission controlled in controller)
Route::get('variant-keys/datatable', 'VariantConfigurationKeyController@datatable')->name('variant-keys.datatable');
Route::get('variant-keys-tree', 'VariantConfigurationKeyController@tree')->name('variant-keys.tree');
Route::resource('variant-keys', 'VariantConfigurationKeyController');

// Variants Configurations (accessible by both admin and vendor - permission controlled in controller)
Route::group(['prefix' => 'variants-configurations'], function() {
    Route::get('datatable', 'VariantsConfigurationController@datatable')->name('variants-configurations.datatable');
    Route::get('get-parents-by-key', 'VariantsConfigurationController@getParentsByKey')->name('variants-configurations.get-parents-by-key');
    Route::get('tree', 'VariantsConfigurationController@tree')->name('variants-configurations.tree');
});
Route::resource('variants-configurations', 'VariantsConfigurationController');

// Occasions

Route::prefix('occasions')->group(function () {
    Route::get('datatable', 'OccasionController@datatable')->name('occasions.datatable');
    Route::post('{id}/toggle-status', 'OccasionController@toggleStatus')->name('occasions.toggle-status');
    Route::delete('{occasion}/products/{product}', 'OccasionController@destroyProduct')->name('occasions.products.destroy');
    Route::post('{occasion}/update-positions', 'OccasionController@updatePositions')->name('occasions.update-positions');
    Route::post('{occasion}/products/{product}/update-special-price', 'OccasionController@updateSpecialPrice')->name('occasions.products.update-special-price');
});
Route::resource('occasions', 'OccasionController');



// Bundles
Route::prefix('bundles')->group(function () {
    Route::get('datatable', 'BundleController@datatable')->name('bundles.datatable');
    Route::post('{id}/toggle-status', 'BundleController@toggleStatus')->name('bundles.toggle-status');
    Route::post('{id}/change-approval', 'BundleController@changeApproval')->name('bundles.change-approval');
    Route::delete('{bundle}/products/{product}', 'BundleController@destroyProduct')->name('bundles.products.destroy');
});
Route::resource('bundles', 'BundleController');


Route::group(['prefix' => 'products'], function() {
    // Products - Static routes MUST come before resource routes
    Route::get('datatable', 'ProductController@datatable')->name('products.datatable');
    
    // Sort order update
    Route::post('update-sort-order', 'ProductController@updateSortOrder')->name('products.update-sort-order');
    
    // Bulk Upload routes
    Route::get('bulk-upload', 'ProductController@bulkUpload')->name('products.bulk-upload');
    Route::post('bulk-upload', 'ProductController@bulkUploadStore')->name('products.bulk-upload.store');
    Route::get('bulk-upload/progress/{batchId}', 'ProductController@checkImportProgress')->name('products.bulk-upload.progress');
    Route::get('download-demo', 'ProductController@downloadDemo')->name('products.download-demo');
    
    // Product status-based routes (must be before resource)
    Route::get('pending', 'ProductController@pending')->name('products.pending');
    Route::get('rejected', 'ProductController@rejected')->name('products.rejected');
    Route::get('accepted', 'ProductController@accepted')->name('products.accepted');
    
    // Search bank products for product creation (accessible with products.create permission)
    Route::get('search-bank-products', 'ProductController@searchBankProducts')->name('products.search-bank-products');

    Route::prefix('bank')->group(function () {
        // Product Bank routes (must be before resource to avoid conflict with {product} parameter)
        Route::get('', 'ProductController@bankProducts')->name('products.bank');
        Route::get('datatable', 'ProductController@bankDatatable')->name('products.bank.datatable');
        Route::get('{id}/view', 'ProductController@bankView')->name('products.bank.view');
        // Vendor Product Management routes
        Route::post('vendor-product/{id}/trash', 'ProductController@trashVendorProduct')->name('products.bank.vendor-product.trash');
        Route::post('vendor-product/{id}/restore', 'ProductController@restoreVendorProduct')->name('products.bank.vendor-product.restore');
        // Bank Stock Management routes - moved to BankController
        Route::get('stock-management', 'BankController@stockManagement')->name('products.bank.stock-management');
        Route::get('api/products', 'BankController@getProducts')->name('products.bank.api.products');
        Route::get('api/taxes', 'BankController@getTaxes')->name('products.bank.api.taxes');
        Route::post('save-stock', 'BankController@saveStock')->name('products.bank.save-stock');
    });

    // Vendor Bank Products - accessible by vendors to see bank products in their departments
    Route::get('vendor-bank', 'ProductController@vendorBankProducts')->name('products.vendor-bank');
    Route::get('vendor-bank/datatable', 'ProductController@vendorBankDatatable')->name('products.vendor-bank.datatable');
    Route::get('vendor-bank/export', 'ProductController@vendorBankExport')->name('products.vendor-bank.export');
    Route::get('vendor-bank/bulk-upload', 'ProductController@vendorBankBulkUpload')->name('products.vendor-bank.bulk-upload');
    Route::post('vendor-bank/bulk-upload', 'ProductController@vendorBankBulkUploadStore')->name('products.vendor-bank.bulk-upload.store');
    Route::get('vendor-bank/download-demo', 'ProductController@vendorBankDownloadDemo')->name('products.vendor-bank.download-demo');
    Route::get('vendor-bank/bulk-upload/progress/{batchId}', 'ProductController@vendorBankCheckImportProgress')->name('products.vendor-bank.bulk-upload.progress');

    // Stock Setup routes
    Route::get('stock-setup', 'StockSetupController@index')->name('products.stock-setup');
    Route::post('stock-setup/save', 'StockSetupController@save')->name('products.stock-setup.save');

    // Product routes with {product} parameter
    Route::get('{product}/stock-management', 'ProductController@stockManagement')->name('products.stock-management');
    Route::post('{product}/update-stock-pricing', 'ProductController@updateStockPricing')->name('products.update-stock-pricing');
    Route::post('{product}/change-status', 'ProductController@changeStatus')->name('products.change-status');
    Route::post('{product}/change-activation', 'ProductController@changeActivation')->name('products.change-activation');
    Route::post('{product}/move-to-bank', 'ProductController@moveToBank')->name('products.move-to-bank');
    Route::post('{product}/change-bank-activation', 'ProductController@changeBankActivation')->name('products.change-bank-activation');


});

// Product resource (must be last)
Route::resource('products', 'ProductController');


// API routes for variant selection in product form
Route::get('api/variant-keys', 'VariantsConfigurationController@getVariantKeys')->name('api.variant-keys');
Route::get('api/variants-by-key', 'VariantsConfigurationController@getVariantsByKey')->name('api.variants-by-key');
