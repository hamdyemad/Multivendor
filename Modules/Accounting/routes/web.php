<?php

use Illuminate\Support\Facades\Route;
use Modules\Accounting\app\Http\Controllers\SummaryController;
use Modules\Accounting\app\Http\Controllers\IncomeController;
use Modules\Accounting\app\Http\Controllers\ExpenseItemController;
use Modules\Accounting\app\Http\Controllers\ExpenseController;
use Modules\Accounting\app\Http\Controllers\BalanceController;
use Modules\Accounting\app\Http\Controllers\VendorBalanceController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('accounting/summary', [SummaryController::class, 'index'])->name('accounting.summary');
    Route::get('accounting/income', [IncomeController::class, 'index'])->name('accounting.income');
    Route::get('accounting/income/datatable', [IncomeController::class, 'datatable'])->name('accounting.income.datatable');

    Route::get('accounting/expense-items', [ExpenseItemController::class, 'index'])->name('accounting.expense-items');
    Route::get('accounting/expense-items/datatable', [ExpenseItemController::class, 'datatable'])->name('accounting.expense-items.datatable');
    Route::post('accounting/expense-items', [ExpenseItemController::class, 'store'])->name('accounting.expense-items.store');
    Route::put('accounting/expense-items/{id}', [ExpenseItemController::class, 'update'])->name('accounting.expense-items.update');
    Route::delete('accounting/expense-items/{id}', [ExpenseItemController::class, 'destroy'])->name('accounting.expense-items.destroy');

    Route::get('accounting/expenses', [ExpenseController::class, 'index'])->name('accounting.expenses');
    Route::get('accounting/expense/datatable', [ExpenseController::class, 'datatable'])->name('accounting.expenses.datatable');
    Route::post('accounting/expenses', [ExpenseController::class, 'store'])->name('accounting.expenses.store');
    Route::put('accounting/expenses/{expense}', [ExpenseController::class, 'update'])->name('accounting.expenses.update');
    Route::delete('accounting/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('accounting.expenses.destroy');

    Route::get('accounting/balances', [VendorBalanceController::class, 'index'])->name('accounting.balances');
    Route::get('accounting/vendor-balances/datatable', [VendorBalanceController::class, 'datatable'])->name('accounting.vendor-balances.datatable');
});
