<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PaginationController;
use App\Http\Controllers\ProfileController;
use Database\Seeders\AutoProductSeeder;
use Database\Seeders\OrderStageSeeder;
use Database\Seeders\CategoryDepartmentSeeder;
use Database\Seeders\TaxSeeder;
use Database\Seeders\VariantConfigurationSeeder;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Order\database\seeders\OrderDatabaseSeeder;
use Modules\CatalogManagement\database\seeders\ReviewSeeder;

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

// Localized routes with language and country code
Route::group([
    'prefix' => LaravelLocalization::setLocale() . '/{countryCode}',
    'middleware' => [
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath',
        'setLocaleFromUrl',
        'auth'
    ],
], function() {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');



    // Route::get('/seeder', function () {
    //     try {
    //     // Seeders in order of dependency
    //     $seeders = [
    //         [
    //             'class' => TaxSeeder::class,
    //             'name' => 'Tax Seeder',
    //             'description' => 'Creates tax rates (VAT 15%, 10%, 5%, etc.)',
    //         ],
    //         [
    //             'class' => VariantConfigurationSeeder::class,
    //             'name' => 'Variant Configuration Seeder',
    //             'description' => 'Creates variant keys (Color, Size, Material) and their values',
    //         ],
    //         [
    //             'class' => CategoryDepartmentSeeder::class,
    //             'name' => 'Category & Department Seeder',
    //             'description' => 'Creates activities, departments, categories, subcategories, brands, and regions',
    //         ],
    //         [
    //             'class' => OrderStageSeeder::class,
    //             'name' => 'Order Stage Seeder',
    //             'description' => 'Creates order stages',
    //         ],
    //         [
    //             'class' => AutoProductSeeder::class,
    //             'name' => 'Auto Product Seeder',
    //             'description' => 'Creates products with variants for each vendor',
    //         ],
    //         [
    //             'class' => OrderDatabaseSeeder::class,
    //             'name' => 'Order Database Seeder',
    //             'description' => 'Creates customers, orders, and order products',
    //         ],
    //         [
    //             'class' => ReviewSeeder::class,
    //             'name' => 'Review Seeder',
    //             'description' => 'Creates customer reviews for products and vendors',
    //         ],
    //     ];

    //     $results = [];
    //     $startTime = microtime(true);

    //     foreach ($seeders as $seeder) {
    //         $seederStartTime = microtime(true);

    //         try {
    //             $exitCode = Artisan::call('db:seed', [
    //                 '--class' => $seeder['class'],
    //                 '--force' => true
    //             ]);

    //             $seederEndTime = microtime(true);
    //             $duration = round($seederEndTime - $seederStartTime, 2);

    //             $results[] = [
    //                 'name' => $seeder['name'],
    //                 'class' => class_basename($seeder['class']),
    //                 'description' => $seeder['description'],
    //                 'exit_code' => $exitCode,
    //                 'duration' => $duration . 's',
    //                 'output' => trim(Artisan::output()),
    //                 'status' => $exitCode === 0 ? 'success' : 'failed',
    //             ];
    //         } catch (\Exception $e) {
    //             $results[] = [
    //                 'name' => $seeder['name'],
    //                 'class' => class_basename($seeder['class']),
    //                 'description' => $seeder['description'],
    //                 'status' => 'error',
    //                 'error' => $e->getMessage(),
    //             ];
    //         }
    //     }

    //     $totalDuration = round(microtime(true) - $startTime, 2);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'All seeders completed!',
    //         'total_duration' => $totalDuration . 's',
    //         'seeders_count' => count($seeders),
    //         'results' => $results,
    //     ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Exception occurred while running seeders',
    //             'error' => $e->getMessage(),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine(),
    //         ], 500);
    //     }
    // });

    // Route::get('/truncate', function(Illuminate\Http\Request $request) {
    //     if ($request->query('key') !== 'MY_SECRET_KEY_123') {
    //         abort(403, 'Unauthorized');
    //     }
    //     DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    //     $tables = [
    //         'activities','activities_departments', 'activity_logs',
    //         'attachments', 'brands', 'bundle_categories', 'categories',
    //         'customers', 'customer_addresses', 'customer_fcm_tokens',
    //         'customer_otps','customer_password_reset_tokens',
    //         'departments', 'orders', 'order_extra_fees_discounts',
    //         'order_fulfillments', 'order_products', 'order_product_taxes',
    //         'products', 'product_variants', 'promocodes', 'reviews', 'sub_categories',
    //         'taxes', 'translations', 'variants_configurations', 'variants_configurations_keys',
    //         'vendors', 'vendors_activities', 'vendor_products', 'vendor_product_variants',
    //         'vendor_product_variant_stocks', 'vendor_regions', 'vendor_requests', 'vendor_requests_activities',
    //         'wishlists', 'withdraws'
    //     ];

    //     foreach ($tables as $table) {
    //         DB::table($table)->truncate();
    //     }

    //     DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    // });
});

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
// Route::get('/pagination-per-page/{per_page}',[ PaginationController::class,'set_pagination_per_page'])->name('pagination_per_page');


// Permession Reset
Route::get('/permissions/reset', function() {
    permessions_reset();
    roles_reset();
    return "done";
});




