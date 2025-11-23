<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\app\Http\Controllers\CustomerController;

Route::group(['prefix' => 'admin/customers', 'as' => 'admin.customers.', 'middleware' => ['auth']], function () {
    Route::get('datatable', [CustomerController::class, 'datatable'])->name('datatable');
});

Route::middleware(['auth'])->name('admin.')->group(function () {
    Route::resource('customers', CustomerController::class)->names('customers');
    Route::get('customers/datatable', [CustomerController::class, 'datatable'])->name('customer.datatable');
});


