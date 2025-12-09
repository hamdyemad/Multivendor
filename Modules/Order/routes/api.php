<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\app\Http\Controllers\Api\WishlistApiController;
use Modules\Order\app\Http\Controllers\Api\CartApiController;
use Modules\Order\app\Http\Controllers\Api\OrderApiController;
use Modules\Order\app\Http\Controllers\Api\OrderStageApiController;

// Public API routes (no authentication required)
Route::prefix('order-stages')->group(function () {
    Route::get('/', [OrderStageApiController::class, 'index'])->name('order-stages.index');
    // Route::get('/{id}', [OrderStageApiController::class, 'show'])->name('order-stages.show');
});

// Order routes
Route::prefix('orders')->group(function () {
    Route::get('{orderId}/allowed-stages', [OrderStageApiController::class, 'allowedStages'])->name('orders.allowed-stages');
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
});
Route::post('/promocode/check', [OrderApiController::class, 'checkPromoCode'])->name('check-promo-code');
