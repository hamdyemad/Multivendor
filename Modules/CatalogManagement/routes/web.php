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

    // Products
    Route::get('products/datatable', 'ProductController@datatable')->name('products.datatable');
    Route::get('products/{product}/stock-management', 'ProductController@stockManagement')->name('products.stock-management');
    Route::post('products/{product}/update-stock-pricing', 'ProductController@updateStockPricing')->name('products.update-stock-pricing');
    Route::post('products/{product}/change-status', 'ProductController@changeStatus')->name('products.change-status');
    Route::post('products/{product}/change-activation', 'ProductController@changeActivation')->name('products.change-activation');

    // Product status-based routes
    Route::get('products/pending', 'ProductController@pending')->name('products.pending');
    Route::get('products/rejected', 'ProductController@rejected')->name('products.rejected');
    Route::get('products/accepted', 'ProductController@accepted')->name('products.accepted');

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
