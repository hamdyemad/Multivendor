<?php

use Illuminate\Support\Facades\Route;
use Modules\CatalogManagement\Http\Controllers\CatalogManagementController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('catalogmanagements', CatalogManagementController::class)->names('catalogmanagement');
});
