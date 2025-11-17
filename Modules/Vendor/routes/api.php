<?php

use Illuminate\Support\Facades\Route;
use Modules\Vendor\app\Http\Controllers\Api\VendorApiController;

Route::apiResource('brands', VendorApiController::class);
