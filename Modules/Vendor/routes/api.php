<?php

use Illuminate\Support\Facades\Route;
use Modules\Vendor\app\Http\Controllers\Api\VendorApiController;

Route::apiResource('vendors', VendorApiController::class);

// Vendor request endpoint
Route::post('vendor-request', [VendorApiController::class, 'vendorRequest']);
