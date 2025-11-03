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
    Route::resource('products', 'ProductController');
    
    // Variant Configuration Keys
    Route::get('variant-keys/datatable', 'VariantConfigurationKeyController@datatable')->name('variant-keys.datatable');
    Route::get('variant-keys-tree', 'VariantConfigurationKeyController@tree')->name('variant-keys.tree');
    Route::resource('variant-keys', 'VariantConfigurationKeyController');
    
    // Variants Configurations
    Route::get('variants-configurations/datatable', 'VariantsConfigurationController@datatable')->name('variants-configurations.datatable');
    Route::get('variants-configurations-tree', 'VariantsConfigurationController@tree')->name('variants-configurations.tree');
    Route::resource('variants-configurations', 'VariantsConfigurationController');
});
