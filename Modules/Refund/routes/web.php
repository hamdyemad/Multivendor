<?php

use Illuminate\Support\Facades\Route;
use Modules\Refund\app\Http\Controllers\RefundRequestController;
use Modules\Refund\app\Http\Controllers\RefundSettingController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Refund Requests Routes
Route::group(['prefix' => 'refunds', 'as' => 'refunds.'], function () {
    Route::get('/', [RefundRequestController::class, 'index'])->name('index');
    Route::get('/datatable', [RefundRequestController::class, 'datatable'])->name('datatable');
    Route::get('/{refundRequest}', [RefundRequestController::class, 'show'])->name('show');
    Route::post('/{refundRequest}/approve', [RefundRequestController::class, 'approve'])->name('approve');
    Route::post('/{refundRequest}/reject', [RefundRequestController::class, 'reject'])->name('reject');
    Route::post('/{refundRequest}/change-status', [RefundRequestController::class, 'changeStatus'])->name('change-status');
    Route::put('/{refundRequest}/notes', [RefundRequestController::class, 'updateNotes'])->name('update-notes');
    
    // Settings route (must be after dynamic routes)
    Route::get('/settings/edit', [RefundSettingController::class, 'index'])->name('settings');
    Route::put('/settings/update', [RefundSettingController::class, 'update'])->name('settings.update');
});
