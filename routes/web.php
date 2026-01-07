<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use Database\Seeders\AutoProductSeeder;
use Database\Seeders\CategoryDepartmentSeeder;
use Database\Seeders\OrderStageSeeder;
use Database\Seeders\TaxSeeder;
use Database\Seeders\VariantConfigurationSeeder;
use Illuminate\Support\Facades\Artisan;
use Modules\CatalogManagement\database\seeders\ReviewSeeder;
use Modules\Order\database\seeders\OrderDatabaseSeeder;

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



Route::get('/preview/{path}', [AuthController::class, 'previewFunc'])
    ->where('path', '.*')
    ->name('preview.file');


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

// Protected routes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Protected routes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Country switch route - updates URL with new country code
Route::get('/switch-country/{countryCode}', function($countryCode) {
    $countryCode = strtoupper($countryCode);
    session()->put('country_code', $countryCode);

    // Get current URL and replace country code
    $previousUrl = url()->previous();
    $parsedUrl = parse_url($previousUrl);
    $path = $parsedUrl['path'] ?? '';
    $segments = explode('/', trim($path, '/'));

    // URL format: /{lang}/{country}/admin/...
    // Replace segment 1 (country code) with new country code
    if (count($segments) >= 3 && $segments[2] === 'admin') {
        // segments[0] = lang, segments[1] = country, segments[2] = admin, ...
        $segments[1] = strtolower($countryCode);
    }

    $newPath = '/' . implode('/', $segments);
    return redirect($newPath);
})->name('switch_country');




// Route::get('/pagination-per-page/{per_page}',[ PaginationController::class,'set_pagination_per_page'])->name('pagination_per_page');


// Permession Reset
Route::get('/permissions/reset', function() {
    permessions_reset();
    roles_reset();
    return "done";
});

// Clear Cache & Optimize
Route::get('/clear-cache', function() {
     Artisan::call('optimize:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
   
    
    return response()->json([
        'success' => true,
        'message' => 'Cache cleared and optimized successfully!',
        'commands' => [
            'optimize:clear',
            'cache:clear',
            'config:clear', 
            'route:clear',
            'view:clear',
        ]
    ]);
})->name('clear_cache');

Route::get('/lang/{lang}',[ LanguageController::class,'switchLang'])->name('switch_lang');

// Country Switch Route
Route::get('/switch-country/{countryCode}', function($countryCode) {
    $country = \Modules\AreaSettings\app\Models\Country::where('code', strtoupper($countryCode))->first();
    if ($country) {
        session()->put('country_code', $country->code);
    }

    // Get current URL and replace country code
    $previousUrl = url()->previous();
    $parsedUrl = parse_url($previousUrl);
    $path = $parsedUrl['path'] ?? '';
    $segments = explode('/', trim($path, '/'));

    // Replace the first segment (country code) with new country code
    if (count($segments) >= 1) {
        $segments[0] = strtolower($countryCode);
    }

    $newPath = '/' . implode('/', $segments);
    return redirect($newPath);
})->name('switch_country');





// Preview email templates
Route::get('/preview-email/welcome', function() {
    $customer = new \Modules\Customer\app\Models\Customer([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com'
    ]);
    
    return view('customer::emails.welcome', compact('customer'));
});

Route::get('/preview-email/otp', function() {
    $otp = '123456';
    $type = request('type', 'email_verification');
    $expiresInMinutes = 10;
    $verificationToken = 'sample-token-123456';
    
    return view('customer::emails.otp', compact('otp', 'type', 'expiresInMinutes', 'verificationToken'));
});
