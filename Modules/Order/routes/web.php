<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\app\Http\Controllers\OrderStageController;
use Modules\Order\app\Http\Controllers\OrderController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'adminGuard'])->group(function () {
    // Order Stages Management
    // Custom routes (must be defined before resource routes)
    Route::get('order-stages/datatable', [OrderStageController::class, 'datatable'])->name('order-stages.datatable');
    Route::post('order-stages/{id}/toggle-status', [OrderStageController::class, 'toggleStatus'])->name('order-stages.toggle-status');

    // Resource routes
    Route::resource('order-stages', OrderStageController::class);

    // Orders Management
    // Custom routes (must be defined before resource routes)
    Route::get('orders/datatable', [OrderController::class, 'datatable'])->name('orders.datatable');
    Route::post('orders/{id}/change-stage', [OrderController::class, 'changeStage'])->name('orders.change-stage');

    // Resource routes
    Route::resource('orders', OrderController::class);

    // Order Fulfillment Management
    Route::prefix('order-fulfillments')->name('order-fulfillments.')->group(function () {
        Route::get('{orderId}/allocate', [\Modules\Order\app\Http\Controllers\OrderFulfillmentController::class, 'show'])->name('show');
        Route::post('{orderId}/allocate', [\Modules\Order\app\Http\Controllers\OrderFulfillmentController::class, 'allocate'])->name('allocate');
    });
});
