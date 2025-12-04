<?php

use Illuminate\Support\Facades\Route;
use Modules\Vendor\app\Http\Controllers\VendorController;
use Modules\Vendor\app\Http\Controllers\VendorRequestController;


// Vendors
Route::group(['prefix' => 'vendors', 'as' => 'vendors.'], function() {
    Route::get('datatable', [VendorController::class, 'datatable'])->name('datatable');
    Route::post('{id}/change-status', [VendorController::class, 'changeStatus'])->name('change-status');
    Route::delete('{vendor}/documents/{document}', [VendorController::class, 'destroyDocument'])->name('documents.destroy');
    Route::resource('', VendorController::class);
});
Route::resource('vendors', VendorController::class);

// Vendor Requests
Route::get('vendor-requests/datatable', [VendorRequestController::class, 'datatable'])->name('vendor-requests.datatable');
Route::post('vendor-requests/{id}/approve', [VendorRequestController::class, 'approve'])->name('vendor-requests.approve');
Route::post('vendor-requests/{id}/reject', [VendorRequestController::class, 'reject'])->name('vendor-requests.reject');
Route::resource('vendor-requests', VendorRequestController::class);
