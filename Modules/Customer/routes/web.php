<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\app\Http\Controllers\CustomerController;
use Modules\Customer\app\Http\Controllers\EmailVerificationController;

// Email verification routes - no auth required
Route::middleware(['web'])->group(function () {
    Route::get('verify-email/{token}', [EmailVerificationController::class, 'verify'])->name('verify-email')->withoutMiddleware(['auth']);
    Route::post('verify-email', [EmailVerificationController::class, 'store'])->name('verify-email.store')->withoutMiddleware(['auth']);
});

Route::get('customers/datatable', [CustomerController::class, 'datatable'])->name('customers.datatable');
Route::post('customers/{id}/change-status', [CustomerController::class, 'changeStatus'])->name('customers.change-status');
Route::post('customers/{id}/change-verification', [CustomerController::class, 'changeVerification'])->name('customers.change-verification');
Route::resource('customers', CustomerController::class)->names('customers');
