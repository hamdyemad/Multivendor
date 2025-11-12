<?php

use Illuminate\Support\Facades\Route;
use Modules\AreaSettings\app\Http\Controllers\Api\RegionController;

Route::apiResource('regions', RegionController::class)->names('regions');
