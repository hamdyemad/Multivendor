<?php

use Illuminate\Support\Facades\Route;
use Modules\Vendor\app\Http\Controllers\VendorController;
use Modules\Vendor\app\Http\Controllers\VendorRequestController;



Route::group(
[
	'prefix' => 'admin',
    'as' => 'admin.'
], function(){
    // Vendors
    Route::get('vendors/datatable', [VendorController::class, 'datatable'])->name('vendors.datatable');
    Route::delete('vendors/{vendor}/documents/{document}', [VendorController::class, 'destroyDocument'])->name('vendors.documents.destroy');
    Route::resource('vendors', VendorController::class);

    // Vendor Requests
    Route::get('vendor-requests/datatable', [VendorRequestController::class, 'datatable'])->name('vendor-requests.datatable');
    Route::post('vendor-requests/{id}/approve', [VendorRequestController::class, 'approve'])->name('vendor-requests.approve');
    Route::post('vendor-requests/{id}/reject', [VendorRequestController::class, 'reject'])->name('vendor-requests.reject');
    Route::resource('vendor-requests', VendorRequestController::class);
});
