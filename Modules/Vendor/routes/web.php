<?php

use Illuminate\Support\Facades\Route;
use Modules\Vendor\app\Http\Controllers\VendorController;



Route::group(
[
	'prefix' => 'admin',
    'as' => 'admin.'
], function(){
    // Vendors
    Route::get('vendors/datatable', [VendorController::class, 'datatable'])->name('vendors.datatable');
    Route::delete('vendors/{vendor}/documents/{document}', [VendorController::class, 'destroyDocument'])->name('vendors.documents.destroy');
    Route::resource('vendors', VendorController::class);
});