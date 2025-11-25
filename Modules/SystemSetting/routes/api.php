<?php

use Illuminate\Support\Facades\Route;
use Modules\SystemSetting\app\Http\Controllers\SystemSettingController;
use Modules\SystemSetting\app\Http\Controllers\Api\MessageApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('systemsettings', SystemSettingController::class)->names('systemsetting');

});

// Messages API
Route::post('messages/send', [MessageApiController::class, 'sendMessage']);
