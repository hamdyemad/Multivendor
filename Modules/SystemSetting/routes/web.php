<?php

use Illuminate\Support\Facades\Route;
use Modules\SystemSetting\app\Http\Controllers\SystemSettingController;
use Modules\SystemSetting\app\Http\Controllers\CurrencyController;
use Modules\SystemSetting\app\Http\Controllers\ActivityLogController;

Route::group(['prefix' => 'admin/system-settings', 'as' => 'admin.system-settings.'], function() {
    // Currencies
    Route::get('currencies/datatable', [CurrencyController::class, 'datatable'])->name('currencies.datatable');
    Route::resource('currencies', CurrencyController::class);

    // Activity Logs
    Route::get('activity-logs/datatable', [ActivityLogController::class, 'datatable'])->name('activity-logs.datatable');
    Route::get('activity-logs/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});
