<?php

use Illuminate\Support\Facades\Route;
use Modules\Withdraw\app\Http\Controllers\WithdrawController;

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function() {
    Route::get('withdraw/send-money', [WithdrawController::class, "sendMoney"])->name("sendMoney");
});
