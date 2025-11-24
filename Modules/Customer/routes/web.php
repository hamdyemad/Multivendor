<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\app\Http\Controllers\CustomerController;

Route::middleware(['auth'])->name('admin.')->group(function () {
    Route::get('customers/datatable', [CustomerController::class, 'datatable'])->name('customers.datatable');
    Route::resource('customers', CustomerController::class)->names('customers');
});
