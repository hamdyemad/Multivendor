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
Route::post('orders/{id}/bulk-change-product-stages', [OrderController::class, 'bulkChangeProductStages'])->name('orders.bulk-change-product-stages');
Route::post('orders/products/{orderProductId}/change-stage', [OrderController::class, 'changeProductStage'])->name('orders.products.change-stage');
Route::post('orders/{orderId}/vendor/{vendorId}/change-stage', [OrderController::class, 'changeVendorStage'])->name('orders.vendor-stage.change');
Route::post('orders/{orderId}/change-all-vendor-stages', [OrderController::class, 'changeAllVendorStages'])->name('orders.change-all-vendor-stages');
Route::get('orders/{id}/payments', [OrderController::class, 'payments'])->name('orders.payments');
Route::get('orders/{id}/print', [OrderController::class, 'print'])->name('orders.print');

// Resource routes
Route::resource('orders', OrderController::class);


// Order Fulfillment Management
Route::prefix('order-fulfillments')->name('order-fulfillments.')->group(function () {
    Route::get('{orderId}/allocate', [OrderFulfillmentController::class, 'show'])->name('show');
    Route::post('{orderId}/allocate', [OrderFulfillmentController::class, 'allocate'])->name('allocate');
});

// Shippings Management
Route::get('shippings/datatable', [ShippingController::class, 'datatable'])->name('shippings.datatable');
Route::post('shippings/update-settings', [ShippingController::class, 'updateSettings'])->name('shippings.update-settings');
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
    Route::post('/{id}/send-offer', [RequestQuotationController::class, 'sendOffer'])->name('send-offer');
    Route::delete('/{id}', [RequestQuotationController::class, 'destroy'])->name('destroy');
    
    // Multi-vendor workflow
    Route::get('/{id}/select-vendors', [RequestQuotationController::class, 'selectVendors'])->name('select-vendors');
    Route::post('/{id}/send-to-vendors', [RequestQuotationController::class, 'sendToVendors'])->name('send-to-vendors');
    Route::get('/{id}/view-offers', [RequestQuotationController::class, 'viewOffers'])->name('view-offers');
});

// Vendor Request Quotations (with localization parameters)
Route::prefix('vendor/request-quotations')->name('vendor.request-quotations.')->group(function () {
    Route::get('/', [\Modules\Order\app\Http\Controllers\VendorRequestQuotationController::class, 'index'])->name('index');
    Route::get('/datatable', [\Modules\Order\app\Http\Controllers\VendorRequestQuotationController::class, 'datatable'])->name('datatable');
    Route::get('/{id}', [\Modules\Order\app\Http\Controllers\VendorRequestQuotationController::class, 'show'])->name('show');
    Route::get('/{id}/create-order-form', [\Modules\Order\app\Http\Controllers\VendorRequestQuotationController::class, 'createOrderForm'])->name('create-order-form');
    Route::post('/{id}/create-order', [\Modules\Order\app\Http\Controllers\VendorRequestQuotationController::class, 'createOrder'])->name('create-order');
});
