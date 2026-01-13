<?php

use Illuminate\Support\Facades\Route;
use Modules\Vendor\app\Http\Controllers\Api\VendorApiController;

Route::apiResource('vendors', VendorApiController::class);

// Vendor request endpoint - with strict rate limiting to prevent spam
Route::post('vendor-request', [VendorApiController::class, 'vendorRequest'])->middleware('throttle:vendor-request');
