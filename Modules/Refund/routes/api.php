<?php

use Illuminate\Support\Facades\Route;
use Modules\Refund\app\Http\Controllers\Api\RefundRequestApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    // Refund Requests
    Route::prefix('refunds')->name('refunds.')->group(function () {
        Route::get('/', [RefundRequestApiController::class, 'index'])->name('index');
        Route::get('/statistics', [RefundRequestApiController::class, 'statistics'])->name('statistics');
        Route::get('/{id}', [RefundRequestApiController::class, 'show'])->name('show');
        Route::post('/', [RefundRequestApiController::class, 'store'])->name('store');
        Route::post('/{id}/status', [RefundRequestApiController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/cancel', [RefundRequestApiController::class, 'cancel'])->name('cancel');
    });
});
