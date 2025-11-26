<?php

use Illuminate\Support\Facades\Route;

Route::group(
[
    'prefix' => 'admin',
    'as' => 'admin.'
], function(){
    // Brands
    Route::get('brands/datatable', 'BrandController@datatable')->name('brands.datatable');
    Route::resource('brands', 'BrandController');

    // Taxes
    Route::get('taxes/datatable', 'TaxController@datatable')->name('taxes.datatable');
    Route::resource('taxes', 'TaxController');

    // Products - Static routes MUST come before resource routes
    Route::get('products/datatable', 'ProductController@datatable')->name('products.datatable');

    // Product Bank routes (must be before resource to avoid conflict with {product} parameter)
    Route::get('products/bank', 'ProductController@bankProducts')->name('products.bank');
    Route::get('products/bank/datatable', 'ProductController@bankDatatable')->name('products.bank.datatable');
    Route::get('products/bank/{id}/view', 'ProductController@bankView')->name('products.bank.view');
    Route::get('products/bank/stock-management', 'ProductController@bankStockManagement')->name('products.bank.stock-management');
    Route::get('products/bank/search', 'ProductController@searchBankProducts')->name('products.bank.search');
    Route::get('products/bank/vendor-product', 'ProductController@getVendorProduct')->name('products.bank.vendor-product');
    Route::get('products/bank/products-not-in-vendor', 'ProductController@getProductsNotInVendor')->name('products.bank.products-not-in-vendor');
    Route::post('products/bank/save-stock', 'ProductController@saveBankStock')->name('products.bank.save-stock');

    // Product status-based routes (must be before resource)
    Route::get('products/pending', 'ProductController@pending')->name('products.pending');
    Route::get('products/rejected', 'ProductController@rejected')->name('products.rejected');
    Route::get('products/accepted', 'ProductController@accepted')->name('products.accepted');

    // Product routes with {product} parameter
    Route::get('products/{product}/stock-management', 'ProductController@stockManagement')->name('products.stock-management');
    Route::post('products/{product}/update-stock-pricing', 'ProductController@updateStockPricing')->name('products.update-stock-pricing');
    Route::post('products/{product}/change-status', 'ProductController@changeStatus')->name('products.change-status');
    Route::post('products/{product}/change-activation', 'ProductController@changeActivation')->name('products.change-activation');
    Route::post('products/{product}/move-to-bank', 'ProductController@moveToBank')->name('products.move-to-bank');
    Route::post('products/{product}/change-bank-activation', 'ProductController@changeBankActivation')->name('products.change-bank-activation');

    // Product resource (must be last)
    Route::resource('products', 'ProductController');

    // Variant Configuration Keys
    Route::get('variant-keys/datatable', 'VariantConfigurationKeyController@datatable')->name('variant-keys.datatable');
    Route::get('variant-keys-tree', 'VariantConfigurationKeyController@tree')->name('variant-keys.tree');
    Route::resource('variant-keys', 'VariantConfigurationKeyController');

    // Variants Configurations
    Route::get('variants-configurations/datatable', 'VariantsConfigurationController@datatable')->name('variants-configurations.datatable');
    Route::get('variants-configurations/get-parents-by-key', 'VariantsConfigurationController@getParentsByKey')->name('variants-configurations.get-parents-by-key');
    Route::get('variants-configurations-tree', 'VariantsConfigurationController@tree')->name('variants-configurations.tree');
    Route::resource('variants-configurations', 'VariantsConfigurationController');

    // API routes for variant selection in product form
    Route::get('api/variant-keys', 'VariantsConfigurationController@getVariantKeys')->name('api.variant-keys');
    Route::get('api/variants-by-key', 'VariantsConfigurationController@getVariantsByKey')->name('api.variants-by-key');
});
