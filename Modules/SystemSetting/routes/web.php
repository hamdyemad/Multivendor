<?php

use Illuminate\Support\Facades\Route;
use Modules\SystemSetting\app\Http\Controllers\SystemSettingController;
use Modules\SystemSetting\app\Http\Controllers\CurrencyController;

Route::group(['prefix' => 'admin/system-settings', 'as' => 'admin.system-settings.'], function() {
    // Currencies
    Route::get('currencies/datatable', [CurrencyController::class, 'datatable'])->name('currencies.datatable');
    Route::resource('currencies', CurrencyController::class);
});
