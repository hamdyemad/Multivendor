<?php

use Illuminate\Support\Facades\Route;
use Modules\SystemSetting\app\Http\Controllers\CurrencyController;
use Modules\SystemSetting\app\Http\Controllers\ActivityLogController;
use Modules\SystemSetting\app\Http\Controllers\MessageController;

Route::group(['prefix' => 'admin/system-settings', 'as' => 'admin.system-settings.'], function() {
    // Currencies
    Route::get('currencies/datatable', [CurrencyController::class, 'datatable'])->name('currencies.datatable');
    Route::resource('currencies', CurrencyController::class);

    // Activity Logs
    Route::get('activity-logs/datatable', [ActivityLogController::class, 'datatable'])->name('activity-logs.datatable');
    Route::get('activity-logs/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

// Messages
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function() {
    Route::get('messages/datatable', [MessageController::class, 'datatable'])->name('messages.datatable');
    Route::delete('messages/{id}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::post('messages/{id}/archive', [MessageController::class, 'archive'])->name('messages.archive');
    Route::get('messages/{id}', [MessageController::class, 'show'])->name('messages.show');
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
});
