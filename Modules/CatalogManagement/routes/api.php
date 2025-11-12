<?php

use Illuminate\Support\Facades\Route;
use Modules\Brands\Http\Controllers\BrandsController;
use Modules\CatalogManagement\app\Http\Controllers\VariantsConfigurationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('brands', BrandsController::class)->names('brands');
});
