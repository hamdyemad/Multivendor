<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\app\Http\Controllers\Api\CustomerApiController;
use Modules\Customer\app\Http\Controllers\Api\CustomerAuthController;
use Modules\Customer\app\Http\Controllers\Api\CustomerAddressController;
use Modules\Customer\app\Http\Controllers\Api\CustomerPointsApiController;

use Modules\Customer\app\Http\Controllers\Api\SubscriptionController;

// Auth routes (no authentication required)
Route::prefix('auth')->group(function () {
    // Registration flow
    Route::post('register', [CustomerAuthController::class, 'register']);
    Route::post('verify-otp', [CustomerAuthController::class, 'verifyOtp']);
    Route::post('verify-email-token', [CustomerAuthController::class, 'verifyEmailToken']);
    Route::post('resend-otp', [CustomerAuthController::class, 'resendOtp']);
    Route::post('refresh', [CustomerAuthController::class, 'refresh']);


    // Login
    Route::post('login', [CustomerAuthController::class, 'login']);

    // Password reset flow
    Route::post('request-password-reset', [CustomerAuthController::class, 'requestPasswordReset']);
    Route::post('verify-reset-otp', [CustomerAuthController::class, 'verifyPasswordResetOtp']);
    Route::post('reset-password', [CustomerAuthController::class, 'resetPassword']);
});

// Protected routes (authentication required)
Route::middleware([
    'auth:sanctum',
    'check.customer.auth'
])->prefix('auth')->group(function () {
    Route::post('logout', [CustomerAuthController::class, 'logout']);
    Route::post('logout-devices', [CustomerAuthController::class, 'logoutDevices']);
    Route::get('profile', [CustomerApiController::class, 'profile']);
    Route::post('update-profile', [CustomerApiController::class, 'updateProfile']);
    Route::post('change-language', [CustomerApiController::class, 'changeLanguage']);
    Route::get('my-addresses', [CustomerApiController::class, 'myAddresses']);
});

// Address routes (authentication required)
Route::middleware(['auth:sanctum', 'check.customer.auth'])->prefix('addresses')->group(function () {
    Route::post('', [CustomerAddressController::class, 'store']);
    Route::get('', [CustomerAddressController::class, 'index']);
    Route::get('{addressId}', [CustomerAddressController::class, 'show']);
    Route::post('{addressId}', [CustomerAddressController::class, 'update']);
    Route::delete('{addressId}', [CustomerAddressController::class, 'destroy']);
});

Route::prefix('customers')->group(function () {
    Route::get('', [CustomerApiController::class, 'index']);
    Route::get('{customerId}/addresses', [CustomerApiController::class, 'getAddresses']);
    Route::post('{customerId}/addresses', [CustomerAddressController::class, 'storeAddress']);
});

// Points routes (authentication required)
Route::middleware(['auth:sanctum', 'check.customer.auth'])->prefix('points')->group(function () {
    Route::get('my-points', [CustomerPointsApiController::class, 'myPoints'])->name('my-points');
    Route::get('transactions', [CustomerPointsApiController::class, 'transactions'])->name('transactions');
    Route::get('settings', [CustomerPointsApiController::class, 'settings'])->name('settings');
});

Route::post('subscriptions', [SubscriptionController::class, 'store']);
