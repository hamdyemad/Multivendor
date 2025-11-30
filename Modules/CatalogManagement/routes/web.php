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

    // Promocodes
    Route::get('promocodes/datatable', 'PromocodeController@datatable')->name('promocodes.datatable');
    Route::post('promocodes/{promocode}/change-status', 'PromocodeController@changeStatus')->name('promocodes.change-status');
    Route::resource('promocodes', 'PromocodeController');

    // Taxes
    Route::get('taxes/datatable', 'TaxController@datatable')->name('taxes.datatable');
    Route::resource('taxes', 'TaxController');


    Route::group(['prefix' => 'products'], function() {
        // Products - Static routes MUST come before resource routes
        Route::get('datatable', 'ProductController@datatable')->name('products.datatable');
        // Product status-based routes (must be before resource)
        Route::get('pending', 'ProductController@pending')->name('products.pending');
        Route::get('rejected', 'ProductController@rejected')->name('products.rejected');
        Route::get('accepted', 'ProductController@accepted')->name('products.accepted');

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


    // Variant Configuration Keys
    Route::get('variant-keys/datatable', 'VariantConfigurationKeyController@datatable')->name('variant-keys.datatable');
    Route::get('variant-keys-tree', 'VariantConfigurationKeyController@tree')->name('variant-keys.tree');
    Route::resource('variant-keys', 'VariantConfigurationKeyController');

    Route::group(['prefix' => 'variants-configurations'], function() {
        // Variants Configurations
        Route::get('datatable', 'VariantsConfigurationController@datatable')->name('variants-configurations.datatable');
        Route::get('get-parents-by-key', 'VariantsConfigurationController@getParentsByKey')->name('variants-configurations.get-parents-by-key');
        Route::get('tree', 'VariantsConfigurationController@tree')->name('variants-configurations.tree');
    });
    Route::resource('variants-configurations', 'VariantsConfigurationController');

    // API routes for variant selection in product form
    Route::get('api/variant-keys', 'VariantsConfigurationController@getVariantKeys')->name('api.variant-keys');
    Route::get('api/variants-by-key', 'VariantsConfigurationController@getVariantsByKey')->name('api.variants-by-key');
});
