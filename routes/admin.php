<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
use Database\Seeders\VendorSeeder;
use Modules\Customer\app\Models\Customer;
use Modules\Vendor\app\Models\Vendor;

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
Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [AdminNotificationController::class, 'index'])->name('index');
    Route::get('/count', [AdminNotificationController::class, 'count'])->name('count');
    Route::get('/{id}', [AdminNotificationController::class, 'show'])->name('show');
    Route::post('/mark-read', [AdminNotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/mark-all-read', [AdminNotificationController::class, 'markAllAsRead'])->name('mark-all-read');
});

// Profile Management
Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});

// Admin Management
Route::prefix('admin-management')->name('admin-management.')->group(function () {
    Route::get('/roles/datatable', [RoleController::class, 'datatable'])->name('roles.data');
    Route::resource('roles', RoleController::class);

    Route::get('/admins/datatable', [AdminController::class, 'datatable'])->name('admins.datatable');
    Route::post('/admins/{admin}/change-status', [AdminController::class, 'changeStatus'])->name('admins.change-status');
    Route::resource('admins', AdminController::class);
});

// Vendor Users Management
Route::prefix('vendor-users-management')->name('vendor-users-management.')->group(function () {
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
    // ===== CREATE BNAIA VENDOR AND UPDATE ALL PRODUCTS/ORDERS =====
    // try {
    //     echo "🏢 Creating/Updating Bnaia Vendor...\n";

    //     echo "✅ Cleanup complete!\n\n";
        
    //     // ===== CREATE BNAIA VENDOR =====
        
    //     // Get or create Bnaia user
    //     $bnaiaUser = \App\Models\User::where('email', 'bnaia@bnaia.com')->first();
        
    //     if (!$bnaiaUser) {
    //         $bnaiaUser = new \App\Models\User();
    //         $bnaiaUser->uuid = \Str::uuid();
    //         $bnaiaUser->email = 'bnaia@bnaia.com';
    //         $bnaiaUser->password = bcrypt('password123');
    //         $bnaiaUser->user_type_id = \App\Models\UserType::VENDOR_TYPE;
    //         $bnaiaUser->active = true;
    //         $bnaiaUser->country_id = 1; // Default to Egypt
    //         $bnaiaUser->save();
            
    //         // Set user translations
    //         $languages = \App\Models\Language::whereIn('code', ['en', 'ar'])->get();
    //         foreach ($languages as $language) {
    //             $bnaiaUser->translations()->create([
    //                 'lang_id' => $language->id,
    //                 'lang_key' => 'name',
    //                 'lang_value' => $language->code === 'en' ? 'Bnaia Admin' : 'مدير بنايا',
    //             ]);
    //         }
    //         echo "  ✓ Created Bnaia user\n";
    //     } else {
    //         echo "  ✓ Bnaia user already exists (ID: {$bnaiaUser->id})\n";
    //     }
        
    //     // Get country ID for vendor
    //     $countryId = session('country_code')
    //         ? \Modules\AreaSettings\app\Models\Country::where('code', strtoupper(session('country_code')))->value('id')
    //         : 1;
        
    //     // Get or create Bnaia vendor
    //     $bnaiaVendor = \Modules\Vendor\app\Models\Vendor::where('slug', 'bnaia')->first();
    //     if (!$bnaiaVendor) {
    //         // Create vendor without triggering events (prevents observer notifications)
    //         $bnaiaVendor = \Modules\Vendor\app\Models\Vendor::withoutEvents(function () use ($bnaiaUser, $countryId) {
    //             $vendor = new \Modules\Vendor\app\Models\Vendor();
    //             $vendor->user_id = $bnaiaUser->id;
    //             $vendor->slug = 'bnaia';
    //             $vendor->phone = '+201000000000';
    //             $vendor->country_id = $countryId;
    //             $vendor->active = true;
    //             $vendor->save();
    //             return $vendor;
    //         });

    //         echo "  ✓ Created Bnaia vendor (ID: {$bnaiaVendor->id}, Country ID: {$countryId})\n";

    //         // Set vendor translations (after vendor is created)
    //         $languages = \App\Models\Language::whereIn('code', ['en', 'ar'])->get();
    //         foreach ($languages as $language) {
    //             $bnaiaVendor->translations()->create([
    //                 'lang_id' => $language->id,
    //                 'lang_key' => 'name',
    //                 'lang_value' => $language->code === 'en' ? 'Bnaia' : 'بنايا',
    //             ]);
    //             $bnaiaVendor->translations()->create([
    //                 'lang_id' => $language->id,
    //                 'lang_key' => 'description',
    //                 'lang_value' => $language->code === 'en' ? 'Bnaia - Building Materials Supplier' : 'بنايا - مورد مواد البناء',
    //             ]);
    //         }
    //         echo "  ✓ Created vendor translations\n";
    //         // Attach logo if exists
    //         $logoPath = public_path('assets/img/logo.png');
    //         if (file_exists($logoPath)) {
    //             echo "  ℹ Logo source found at: {$logoPath}\n";

    //             // Delete existing logo attachment if any
    //             \App\Models\Attachment::where('attachable_type', \Modules\Vendor\app\Models\Vendor::class)
    //                 ->where('attachable_id', $bnaiaVendor->id)
    //                 ->where('type', 'logo')
    //                 ->delete();

    //             // Copy logo to storage/app/public/vendor-images/
    //             $storagePath = 'vendor-images/logo.png';
    //             $destinationPath = public_path('storage/' . $storagePath);

    //             echo "  ℹ Copying to: {$destinationPath}\n";

    //             // Create directory if it doesn't exist
    //             $directory = dirname($destinationPath);
    //             if (!file_exists($directory)) {
    //                 mkdir($directory, 0755, true);
    //                 echo "  ℹ Created directory: {$directory}\n";
    //             }

    //             // Copy the logo file
    //             copy($logoPath, $destinationPath);

    //             if (file_exists($destinationPath)) {
    //                 echo "  ℹ Logo copied successfully\n";
    //             }

    //             $attachment = new \App\Models\Attachment();
    //             $attachment->attachable_type = \Modules\Vendor\app\Models\Vendor::class;
    //             $attachment->attachable_id = $bnaiaVendor->id;
    //             $attachment->type = 'logo';
    //             $attachment->path = $storagePath;
    //             $attachment->save();

    //             echo "  ✓ Attached logo to Bnaia vendor (path: {$storagePath})\n";
    //             echo "  ℹ Attachment ID: {$attachment->id}\n";

    //             // Verify attachment was saved
    //             $savedAttachment = \App\Models\Attachment::where('attachable_type', \Modules\Vendor\app\Models\Vendor::class)
    //                 ->where('attachable_id', $bnaiaVendor->id)
    //                 ->where('type', 'logo')
    //                 ->first();

    //             if ($savedAttachment) {
    //                 echo "  ℹ Attachment verified in database (ID: {$savedAttachment->id}, Path: {$savedAttachment->path})\n";
    //             } else {
    //                 echo "  ⚠ Attachment not found in database!\n";
    //             }
    //         } else {
    //             echo "  ⚠ Logo source not found at: {$logoPath}\n";
    //         }

    //         // Assign all departments to Bnaia vendor
    //         $allDepartments = \Modules\CategoryManagment\app\Models\Department::pluck('id')->toArray();
    //         if (!empty($allDepartments)) {
    //             $bnaiaVendor->departments()->sync($allDepartments);
    //             echo "  ✓ Assigned " . count($allDepartments) . " departments to Bnaia vendor\n";
    //         }

    //         // Assign all regions to Bnaia vendor
    //         $allRegions = \Modules\AreaSettings\app\Models\Region::pluck('id')->toArray();
    //         if (!empty($allRegions)) {
    //             $bnaiaVendor->regions()->sync($allRegions);
    //             echo "  ✓ Assigned " . count($allRegions) . " regions to Bnaia vendor\n";
    //         }
    //     }
        
    //     // Update all vendor_products to use Bnaia vendor
    //     $updatedVendorProducts = \DB::table('vendor_products')
    //         ->where('vendor_id', '!=', $bnaiaVendor->id)
    //         ->update(['vendor_id' => $bnaiaVendor->id]);
    //     echo "  ✓ Updated {$updatedVendorProducts} vendor products to Bnaia\n";
        
    //     // Update all order_products to use Bnaia vendor
    //     $updatedOrderProducts = \DB::table('order_products')
    //         ->where('vendor_id', '!=', $bnaiaVendor->id)
    //         ->update(['vendor_id' => $bnaiaVendor->id]);
    //     echo "  ✓ Updated {$updatedOrderProducts} order products to Bnaia\n";
        
    //     // Update all vendor_order_stages to use Bnaia vendor
    //     $updatedVendorStages = \DB::table('vendor_order_stages')
    //         ->where('vendor_id', '!=', $bnaiaVendor->id)
    //         ->update(['vendor_id' => $bnaiaVendor->id]);
    //     echo "  ✓ Updated {$updatedVendorStages} vendor order stages to Bnaia\n";
        
    //     echo "✅ Bnaia vendor setup complete!\n\n";
        
    // } catch (\Exception $e) {
    //     echo "❌ Error setting up Bnaia vendor: {$e->getMessage()}\n\n";
    // }

    // permessions_reset();
    // roles_reset();

    // // Set email_verified_at for all customers
    // \Modules\Customer\app\Models\Customer::whereNull('email_verified_at')
    //     ->update(['email_verified_at' => now()]);

    // // Delete orders and withdraws data
    \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    // \Modules\Order\app\Models\OrderProduct::query()->forceDelete();
    // \Modules\Order\app\Models\Order::query()->forceDelete();
    \Modules\Order\app\Models\OrderStage::query()->forceDelete();
    // \Modules\Order\app\Models\OrderExtraFeeDiscount::query()->forceDelete();
    // \Modules\Order\app\Models\VendorOrderStage::query()->forceDelete();
    // // \Modules\Order\app\Models\RequestQuotation::query()->forceDelete();
    // \Modules\Withdraw\app\Models\Withdraw::query()->forceDelete();
    // \Modules\CatalogManagement\app\Models\StockBooking::query()->forceDelete();
    // // \Modules\CatalogManagement\app\Models\Review::query()->forceDelete();

    // // // Delete accounting entries
    // \Modules\Accounting\app\Models\AccountingEntry::query()->forceDelete();
    // \Modules\Accounting\app\Models\Expense::query()->forceDelete();
    // \Modules\Accounting\app\Models\ExpenseItem::query()->forceDelete();
    // \Modules\Accounting\app\Models\VendorBalance::query()->forceDelete();
    // \Modules\Refund\app\Models\RefundRequest::query()->forceDelete();
    // \Modules\Refund\app\Models\RefundRequestHistory::query()->forceDelete();
    // \Modules\Refund\app\Models\RefundRequestItem::query()->forceDelete();

    // // Delete user points and transactions
    // \DB::statement('DELETE FROM user_points_transactions');
    // \DB::statement('DELETE FROM user_points');

    // \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    // // Update product configuration_type based on vendor product variants
    // // If any variant has variant_configuration_id, set product to 'variants'
    // $productsToUpdate = \DB::table('products as p')
    //     ->join('vendor_products as vp', 'vp.product_id', '=', 'p.id')
    //     ->join('vendor_product_variants as vpv', 'vpv.vendor_product_id', '=', 'vp.id')
    //     ->whereNotNull('vpv.variant_configuration_id')
    //     ->where('p.configuration_type', 'simple')
    //     ->distinct()
    //     ->pluck('p.id');

    // $productsUpdatedCount = 0;
    // if ($productsToUpdate->count() > 0) {
    //     $productsUpdatedCount = \DB::table('products')
    //         ->whereIn('id', $productsToUpdate)
    //         ->update(['configuration_type' => 'variants']);
    // }

    // // Log how many products were updated
    // $variantProductsCount = \Modules\CatalogManagement\app\Models\Product::where('configuration_type', 'variants')->count();
    // \Illuminate\Support\Facades\Log::info("Products updated to variants: {$productsUpdatedCount}, Total variant products: {$variantProductsCount}");

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
            //     'class' => \Database\Seeders\ProductVariantSeeder::class,
            //     'name' => 'Product Variant Seeder',
            //     'description' => 'Creates ProductVariant records for products with VendorProductVariants',
            // ],
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
