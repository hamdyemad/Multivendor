<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\app\Http\Controllers\Api\WishlistApiController;
use Modules\Order\app\Http\Controllers\Api\CartApiController;
use Modules\Order\app\Http\Controllers\Api\OrderApiController;
use Modules\Order\app\Http\Controllers\Api\OrderStageApiController;
use Modules\Order\app\Http\Controllers\Api\ShippingCalculationController;
use Modules\Order\app\Http\Controllers\Api\RequestQuotationApiController;
use Modules\Order\app\Http\Controllers\Api\PaymobController;
use Modules\Order\app\Http\Controllers\Api\PaymobWebhookController;

// Public API routes (no authentication required)
Route::prefix('order-stages')->group(function () {
    Route::get('/', [OrderStageApiController::class, 'index'])->name('order-stages.index');
    // Route::get('/{id}', [OrderStageApiController::class, 'show'])->name('order-stages.show');
});

// Order routes
Route::prefix('orders')->group(function () {
    Route::get('{orderId}/allowed-stages', [OrderStageApiController::class, 'allowedStages'])->name('orders.allowed-stages');
});

// Request Quotation API (public - no auth required)
Route::post('/request-quotations', [RequestQuotationApiController::class, 'store'])->name('request-quotations.store');

// Paymob Payment Routes (public - no auth required for webhooks/callbacks)
Route::prefix('paymob')->group(function () {
    Route::post('/create', [PaymobController::class, 'createPayment'])->name('paymob.create');
    Route::match(['get', 'post'], '/webhook', [PaymobWebhookController::class, 'handle'])->name('paymob.webhook');
    Route::match(['get', 'post'], '/callback', [PaymobController::class, 'callback'])->name('paymob.callback');
    Route::get('/check/{paymob_order_id}', [PaymobController::class, 'checkPayment'])->name('paymob.check');
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Wishlist API routes
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistApiController::class, 'list']);
        Route::post('/add', [WishlistApiController::class, 'add']);
        Route::post('/remove', [WishlistApiController::class, 'remove']);
        Route::post('/clear', [WishlistApiController::class, 'clear']);
        Route::get('/check', [WishlistApiController::class, 'check']);
        Route::get('/count', [WishlistApiController::class, 'count']);
    });

    // Cart API routes
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartApiController::class, 'list']);
        Route::get('/check', [CartApiController::class, 'check']);
        Route::get('/count', [CartApiController::class, 'count']);
        Route::get('/summary', [CartApiController::class, 'summary']);
        Route::post('/add-or-update', [CartApiController::class, 'addOrUpdate']);
        Route::post('/add-bulk', [CartApiController::class, 'addBulk']);
        Route::delete('/remove/{id}', [CartApiController::class, 'remove']);
        Route::post('/clear', [CartApiController::class, 'clear']);
    });

    // Order API routes
    Route::prefix('orders')->group(function () {
        Route::post('/checkout', [OrderApiController::class, 'checkout'])->name('checkout');
        Route::get('/', [OrderApiController::class, 'myOrders'])->name('my-orders');
        Route::get('/{orderId}', [OrderApiController::class, 'show'])->name('show');
        Route::post('/{orderId}/cancel', [OrderApiController::class, 'cancel'])->name('cancel');
        Route::post('/{orderId}/return', [OrderApiController::class, 'return'])->name('return');
    });

    // Shipping calculation routes
    Route::prefix('shipping')->group(function () {
        Route::post('/calculate', [ShippingCalculationController::class, 'calculate'])->name('calculate');
    });
});
Route::post('/promocode/check', [OrderApiController::class, 'checkPromoCode'])->name('check-promo-code');
