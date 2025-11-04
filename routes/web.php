<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PaginationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => '/', 'middleware' => 'guest'], function() {
    Route::get('/',[AuthController::class,'login'])->name('login');
    Route::post('/',[AuthController::class,'authenticate'])->name('authenticate');
    Route::group(['prefix' => 'forget-password', 'as' => 'forgetPassword.'], function() {
        Route::get('/',[AuthController::class,'forgetPasswordView'])->name('index');
        Route::post('/',[AuthController::class,'forgetPassword'])->name('store');
        Route::get('/{user}/reset',[AuthController::class,'resetPasswordView'])->name('reset');
        Route::post('/{user}/reset',[AuthController::class,'resetPassword'])->name('reset-store');
    });
});

Route::post('/logout',[AuthController::class,'logout'])->name('logout')->middleware('auth');

Route::get('/lang/{lang}',[ LanguageController::class,'switchLang'])->name('switch_lang');
// Route::get('/pagination-per-page/{per_page}',[ PaginationController::class,'set_pagination_per_page'])->name('pagination_per_page');


// Permession Reset
Route::get('/permissions/reset', function() {
    permessions_reset();
    roles_reset();
    return "done";
});