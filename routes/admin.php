<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PaginationController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\AdminManagement\RoleController;
use App\Http\Controllers\AdminManagement\AdminController;
use App\Http\Controllers\AdminManagement\VendorUserController;
use App\Http\Controllers\AreaSettings\CountryController;
use App\Http\Controllers\AreaSettings\CityController;
use App\Http\Controllers\AreaSettings\RegionController;
use App\Http\Controllers\AreaSettings\SubRegionController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\Api\InjectDataController;
use App\Http\Controllers\Admin\TruncateController;
use Database\Seeders\OrderStageSeeder;
use Database\Seeders\SyncVendorUsersSeeder;
use Database\Seeders\VendorProductTaxSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

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

// Inject data from external source (with lang and country)
Route::get('inject-data', [InjectDataController::class, 'inject'])->name('inject-data');

// Admin Notifications
Route::prefix('notifications')->name('notifications.')->group(function() {
    Route::post('/mark-read', [AdminNotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/mark-all-read', [AdminNotificationController::class, 'markAllAsRead'])->name('mark-all-read');
});

// Profile Management
Route::prefix('profile')->name('profile.')->group(function() {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});

// Admin Management
Route::prefix('admin-management')->name('admin-management.')->group(function() {
    Route::get('/roles/datatable', [RoleController::class, 'datatable'])->name('roles.data');
    Route::resource('roles', RoleController::class);

    Route::get('/admins/datatable', [AdminController::class, 'datatable'])->name('admins.datatable');
    Route::post('/admins/{admin}/change-status', [AdminController::class, 'changeStatus'])->name('admins.change-status');
    Route::resource('admins', AdminController::class);
});

// Vendor Users Management
Route::prefix('vendor-users-management')->name('vendor-users-management.')->group(function() {
    Route::get('/roles/datatable', [RoleController::class, 'vendorUserRolesDatatable'])->name('roles.data');
    Route::get('/roles/by-vendor', [RoleController::class, 'getRolesByVendor'])->name('roles.by-vendor');
    Route::get('/roles', [RoleController::class, 'vendorUserRolesIndex'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'vendorUserRolesCreate'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'vendorUserRolesStore'])->name('roles.store');
    Route::get('/roles/{role}', [RoleController::class, 'vendorUserRolesShow'])->name('roles.show');
    Route::get('/roles/{role}/edit', [RoleController::class, 'vendorUserRolesEdit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'vendorUserRolesUpdate'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'vendorUserRolesDestroy'])->name('roles.destroy');

    Route::get('/vendor-users/datatable', [VendorUserController::class, 'datatable'])->name('vendor-users.datatable');
    Route::post('/vendor-users/{vendor_user}/change-status', [VendorUserController::class, 'changeStatus'])->name('vendor-users.change-status');
    Route::resource('vendor-users', VendorUserController::class);
});


Route::get('seeder', function () {
        permessions_reset();
        roles_reset();
        
        // Delete orders and withdraws data
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \Modules\Order\app\Models\OrderProduct::query()->forceDelete();
        \Modules\Order\app\Models\Order::query()->forceDelete();
        \Modules\Order\app\Models\OrderExtraFeeDiscount::query()->forceDelete();
        \Modules\Order\app\Models\VendorOrderStage::query()->forceDelete();
        \Modules\Withdraw\app\Models\Withdraw::query()->forceDelete();
        \Modules\CatalogManagement\app\Models\StockBooking::query()->forceDelete();
        
        // Delete accounting entries
        \Modules\Accounting\app\Models\AccountingEntry::query()->forceDelete();
        \Modules\Accounting\app\Models\Expense::query()->forceDelete();
        \Modules\Accounting\app\Models\ExpenseItem::query()->forceDelete();
        \Modules\Accounting\app\Models\VendorBalance::query()->forceDelete();
        
        // Delete user points and transactions
        \DB::statement('DELETE FROM user_points_transactions');
        \DB::statement('DELETE FROM user_points');
        
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        try {
        // Seeders in order of dependency
        $seeders = [
            // [
            //     'class' => AreaSettingsSeeder::class,
            //     'name' => 'Area Settings Seeder',
            //     'description' => 'Creates cities, regions, and subregions for Egypt and Saudi Arabia',
            // ],
            // [
            //     'class' => TaxSeeder::class,
            //     'name' => 'Tax Seeder',
            //     'description' => 'Creates tax rates (VAT 15%, 10%, 5%, etc.)',
            // ],
            // [
            //     'class' => VariantConfigurationSeeder::class,
            //     'name' => 'Variant Configuration Seeder',
            //     'description' => 'Creates variant keys (Color, Size, Material) and their values',
            // ],
            // [
            //     'class' => CategoryDepartmentSeeder::class,
            //     'name' => 'Category & Department Seeder',
            //     'description' => 'Creates departments, categories, subcategories, brands, and regions',
            // ],
            // [
            //     'class' => BrandSeeder::class,
            //     'name' => 'Brand Seeder',
            //     'description' => 'Creates brands with country_id and translations',
            // ],
            // [
            //     'class' => VendorSeeder::class,
            //     'name' => 'Vendor Seeder',
            //     'description' => 'Creates vendors with country_id and translations',
            // ],
            [
                'class' => OrderStageSeeder::class,
                'name' => 'Order Stage Seeder',
                'description' => 'Creates order stages',
            ],
            // [
            //     'class' => AutoProductSeeder::class,
            //     'name' => 'Auto Product Seeder',
            //     'description' => 'Creates products with variants for each vendor',
            // ],
            // [
            //     'class' => ReviewSeeder::class,
            //     'name' => 'Review Seeder',
            //     'description' => 'Creates customer reviews for products and vendors',
            // ],
            // [
            //     'class' => CustomerSeeder::class,
            //     'name' => 'Customer Seeder',
            //     'description' => 'Creates 10 sample customers with contact information',
            // ],
            // [
            //     'class' => OrderSeeder::class,
            //     'name' => 'Order Seeder',
            //     'description' => 'Creates 30 sample orders with products, pricing, and shipping',
            // ],
            // [
            //     'class' => SyncVendorUsersSeeder::class,
            //     'name' => 'SyncVendorUsersSeeder',
            //     'description' => 'Update Vendor Users',
            // ],
            // [
            //     'class' => VendorProductTaxSeeder::class,
            //     'name' => 'VendorProductTaxSeeder',
            //     'description' => 'Assign all active taxes to every vendor product',
            // ],
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

Route::get('/truncate', [TruncateController::class, 'truncate'])->name('truncate');







