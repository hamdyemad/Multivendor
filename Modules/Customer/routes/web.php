<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\app\Http\Controllers\CustomerController;
use Modules\Customer\app\Http\Controllers\EmailVerificationController;

// Email verification routes - no auth required
Route::middleware(['web'])->group(function () {
    Route::get('verify-email/{token}', [EmailVerificationController::class, 'verify'])->name('verify-email')->withoutMiddleware(['auth']);
    Route::post('verify-email', [EmailVerificationController::class, 'store'])->name('verify-email.store')->withoutMiddleware(['auth']);
});

Route::middleware(['auth'])->name('admin.')->group(function () {
    Route::get('customers/datatable', [CustomerController::class, 'datatable'])->name('customers.datatable');
    Route::resource('customers', CustomerController::class)->names('customers');
});
