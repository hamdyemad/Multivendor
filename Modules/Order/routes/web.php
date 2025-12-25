<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\app\Http\Controllers\OrderStageController;
use Modules\Order\app\Http\Controllers\OrderController;
use Modules\Order\app\Http\Controllers\ShippingController;
use Modules\Order\app\Http\Controllers\ShippingCalculationController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Order\app\Http\Controllers\OrderFulfillmentController;
use Modules\Order\app\Http\Controllers\RequestQuotationController;

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
Route::get('orders/{id}/payments', [OrderController::class, 'payments'])->name('orders.payments');

// Resource routes
Route::resource('orders', OrderController::class);


// Order Fulfillment Management
Route::prefix('order-fulfillments')->name('order-fulfillments.')->group(function () {
    Route::get('{orderId}/allocate', [OrderFulfillmentController::class, 'show'])->name('show');
    Route::post('{orderId}/allocate', [OrderFulfillmentController::class, 'allocate'])->name('allocate');
});

// Shippings Management
Route::get('shippings/datatable', [ShippingController::class, 'datatable'])->name('shippings.datatable');
Route::post('shippings/{id}/change-status', [ShippingController::class, 'changeStatus'])->name('shippings.change-status');
Route::resource('shippings', ShippingController::class);

// Shipping Calculation
Route::post('shipping/calculate', [ShippingCalculationController::class, 'calculate'])->name('shipping.calculate');

// Request Quotations Management
Route::prefix('request-quotations')->name('request-quotations.')->group(function () {
    Route::get('/', [RequestQuotationController::class, 'index'])->name('index');
    Route::get('/archived', [RequestQuotationController::class, 'archived'])->name('archived');
    Route::get('/datatable', [RequestQuotationController::class, 'datatable'])->name('datatable');
    Route::post('/{id}/archive', [RequestQuotationController::class, 'archive'])->name('archive');
    Route::delete('/{id}', [RequestQuotationController::class, 'destroy'])->name('destroy');
});
