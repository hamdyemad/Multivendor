<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PaginationController;
use App\Http\Controllers\ProfileController;

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

// Landing page (accessible to everyone)
Route::get('/landing', function() {
    return view('landing');
})->name('landing');

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

// Profile Routes
Route::middleware('auth')->group(function() {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

Route::get('/lang/{lang}',[ LanguageController::class,'switchLang'])->name('switch_lang');
// Route::get('/pagination-per-page/{per_page}',[ PaginationController::class,'set_pagination_per_page'])->name('pagination_per_page');


// Permession Reset
Route::get('/permissions/reset', function() {
    permessions_reset();
    roles_reset();
    return "done";
});


Route::get('/seeder', function() {
    try {
        $exitCode = \Artisan::call('db:seed', ['--class' => 'AutoProductSeeder']);
        $output = \Artisan::output();

        if ($exitCode === 0) {
            return response()->json([
                'success' => true,
                'message' => 'AutoProductSeeder completed successfully!',
                'output' => $output,
                'exit_code' => $exitCode
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Seeder failed to run',
                'output' => $output, // This will show the actual error from the seeder
                'exit_code' => $exitCode
            ], 500);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Exception occurred while running seeder',
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
