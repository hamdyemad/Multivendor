<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PaginationController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AdminManagement\RoleController;
use App\Http\Controllers\AdminManagement\AdminController;
use App\Http\Controllers\AreaSettings\CountryController;
use App\Http\Controllers\AreaSettings\CityController;
use App\Http\Controllers\AreaSettings\RegionController;
use App\Http\Controllers\AreaSettings\SubRegionController;
use App\Http\Controllers\DepartmentController;
use App\Models\Translation;
use Database\Seeders\AreaSettingsSeeder;
use Database\Seeders\AutoProductSeeder;
use Database\Seeders\BrandSeeder;
use Database\Seeders\CategoryDepartmentSeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\OrderSeeder;
use Database\Seeders\OrderStageSeeder;
use Database\Seeders\TaxSeeder;
use Database\Seeders\VariantConfigurationSeeder;
use Database\Seeders\VendorSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Modules\AreaSettings\app\Models\Country;
use Modules\CatalogManagement\database\seeders\ReviewSeeder;
use Modules\Order\database\seeders\OrderDatabaseSeeder;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| These routes are protected by auth middleware from RouteServiceProvider
|
*/


// Admin dashboard with country code
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Admin Management
Route::prefix('admin-management')->name('admin-management.')->group(function() {
    Route::get('/roles/datatable', [RoleController::class, 'datatable'])->name('roles.data');
    Route::resource('roles', RoleController::class);

    Route::get('/admins/datatable', [AdminController::class, 'datatable'])->name('admins.datatable');
    Route::resource('admins', AdminController::class);
});


Route::get('seeder', function () {
        try {
        // Seeders in order of dependency
        $seeders = [
            [
                'class' => AreaSettingsSeeder::class,
                'name' => 'Area Settings Seeder',
                'description' => 'Creates cities, regions, and subregions for Egypt and Saudi Arabia',
            ],
            [
                'class' => TaxSeeder::class,
                'name' => 'Tax Seeder',
                'description' => 'Creates tax rates (VAT 15%, 10%, 5%, etc.)',
            ],
            [
                'class' => VariantConfigurationSeeder::class,
                'name' => 'Variant Configuration Seeder',
                'description' => 'Creates variant keys (Color, Size, Material) and their values',
            ],
            [
                'class' => CategoryDepartmentSeeder::class,
                'name' => 'Category & Department Seeder',
                'description' => 'Creates departments, categories, subcategories, brands, and regions',
            ],
            [
                'class' => BrandSeeder::class,
                'name' => 'Brand Seeder',
                'description' => 'Creates brands with country_id and translations',
            ],
            [
                'class' => VendorSeeder::class,
                'name' => 'Vendor Seeder',
                'description' => 'Creates vendors with country_id and translations',
            ],
            [
                'class' => OrderStageSeeder::class,
                'name' => 'Order Stage Seeder',
                'description' => 'Creates order stages',
            ],
            [
                'class' => AutoProductSeeder::class,
                'name' => 'Auto Product Seeder',
                'description' => 'Creates products with variants for each vendor',
            ],
            [
                'class' => ReviewSeeder::class,
                'name' => 'Review Seeder',
                'description' => 'Creates customer reviews for products and vendors',
            ],
            [
                'class' => CustomerSeeder::class,
                'name' => 'Customer Seeder',
                'description' => 'Creates 10 sample customers with contact information',
            ],
            [
                'class' => OrderSeeder::class,
                'name' => 'Order Seeder',
                'description' => 'Creates 30 sample orders with products, pricing, and shipping',
            ],
        ];

        $results = [];
        $startTime = microtime(true);

        foreach ($seeders as $seeder) {
            $seederStartTime = microtime(true);

            try {
                $exitCode = Artisan::call('db:seed', [
                    '--class' => $seeder['class'],
                    '--force' => true
                ]);

                $seederEndTime = microtime(true);
                $duration = round($seederEndTime - $seederStartTime, 2);

                $results[] = [
                    'name' => $seeder['name'],
                    'class' => class_basename($seeder['class']),
                    'description' => $seeder['description'],
                    'exit_code' => $exitCode,
                    'duration' => $duration . 's',
                    'output' => trim(Artisan::output()),
                    'status' => $exitCode === 0 ? 'success' : 'failed',
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'name' => $seeder['name'],
                    'class' => class_basename($seeder['class']),
                    'description' => $seeder['description'],
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        }

        $totalDuration = round(microtime(true) - $startTime, 2);

        return response()->json([
            'success' => true,
            'message' => 'All seeders completed!',
            'total_duration' => $totalDuration . 's',
            'seeders_count' => count($seeders),
            'results' => $results,
        ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Exception occurred while running seeders',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
});

Route::get('/truncate', function(Illuminate\Http\Request $request) {
    if ($request->query('key') !== 'MY_SECRET_KEY_123') {
        abort(403, 'Unauthorized');
    }
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Translation::latest()->forceDelete();
    $tables = [
        'activity_logs','cities', 'regions', 'subregions', 'vendor_regions',
        'attachments', 'brands', 'bundle_categories', 'categories',
        'departments',
        'customers',
        'customer_addresses', 'customer_fcm_tokens',
        'customer_otps','customer_password_reset_tokens',
        'orders', 'order_extra_fees_discounts', 'order_stages',
        'order_fulfillments', 'order_products', 'order_product_taxes',
        'products', 'product_variants', 'promocodes', 'reviews', 'sub_categories',
        'taxes', 'translations', 'variants_configurations', 'variants_configurations_keys',
        'vendors', 'vendor_products', 'vendor_product_variants',
        'vendor_product_variant_stocks', 'vendor_requests',
        'wishlists', 'withdraws'
    ];

    foreach ($tables as $table) {
        DB::table($table)->truncate();
    }

    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
});







