<?php

use Illuminate\Support\Facades\Route;
use Modules\CatalogManagement\Http\Controllers\CatalogManagementController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('catalogmanagements', CatalogManagementController::class)->names('catalogmanagement');
});
