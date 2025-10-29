<?php

use Illuminate\Support\Facades\Route;
use Modules\Brands\app\Http\Controllers\BrandController;

Route::group(
[
    'prefix' => 'admin',
    'as' => 'admin.'
], function(){
    // Brands
    Route::get('brands/datatable', [BrandController::class, 'datatable'])->name('brands.datatable');
    Route::get('brands/search', [BrandController::class, 'brandSearch'])->name('brands.search');
    Route::resource('brands', BrandController::class);
});
