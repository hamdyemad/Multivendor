<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Modules\CategoryManagment\app\Models\Department;
use Modules\CategoryManagment\app\Models\Category;
use Modules\CategoryManagment\app\Models\SubCategory;
use Modules\CatalogManagement\app\Models\VariantConfigurationKey;
use Modules\CatalogManagement\app\Models\VariantsConfiguration;
use Modules\CatalogManagement\app\Models\Brand;
use Modules\CatalogManagement\app\Models\Tax;
use Modules\CatalogManagement\app\Models\Occasion;
use Modules\CatalogManagement\app\Models\OccasionProduct;
use Modules\CatalogManagement\app\Models\Product;
use Modules\CatalogManagement\app\Models\ProductVariant;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Modules\CatalogManagement\app\Models\VendorProductVariantStock;
use Modules\SystemSetting\app\Models\BlogCategory;
use Modules\SystemSetting\app\Models\Blog;
use Modules\SystemSetting\app\Models\AdPosition;
use Modules\SystemSetting\app\Models\Ad;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Region;
use Modules\AreaSettings\app\Models\Country;
use Modules\Order\app\Models\Shipping;
use Modules\Vendor\app\Models\Vendor;
use Modules\Customer\app\Models\Customer;
use Modules\SystemSetting\app\Models\UserPoints;
use Modules\CatalogManagement\app\Models\BundleCategory;
use Modules\CatalogManagement\app\Models\Bundle;
use Modules\CatalogManagement\app\Models\BundleProduct;
use App\Models\User;
use App\Models\UserType;
use App\Models\Role;
use App\Models\Attachment;

class InjectDataController extends Controller
{
    protected string $sourceBaseUrl = 'https://dashboard-oldversion.bnaia.com';

    /**
     * Configuration for truncating data before injection
     */
    protected array $truncateConfig = [
        'departments' => [
            'tables' => ['departments'],
            'folders' => ['department-images'],
            'attachable_type' => 'Modules\\CategoryManagment\\app\\Models\\Department',
        ],
        'main_categories' => [
            'tables' => ['categories'],
            'folders' => ['category-images', 'main-category-images'],
            'attachable_type' => 'Modules\\CategoryManagment\\app\\Models\\Category',
        ],
        'sub_categories' => [
            'tables' => ['sub_categories'],
            'folders' => ['sub-category-images'],
            'attachable_type' => 'Modules\\CategoryManagment\\app\\Models\\SubCategory',
        ],
        'variant_keys' => [
            'tables' => ['variants_configurations_keys'],
            'folders' => [],
            'attachable_type' => 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey',
        ],
        'variants' => [
            'tables' => ['variants_configurations'],
            'folders' => [],
            'attachable_type' => 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration',
        ],
        'brands' => [
            'tables' => ['brands', 'vendors'],
            'folders' => ['brands-images', 'vendor-images'],
            'attachable_types' => [
                'Modules\\CatalogManagement\\app\\Models\\Brand',
                'Modules\\Vendor\\app\\Models\\Vendor',
            ],
            'delete_vendor_users' => true,
        ],
        'taxes' => [
            'tables' => ['taxes'],
            'folders' => [],
            'attachable_type' => 'Modules\\CatalogManagement\\app\\Models\\Tax',
        ],
        'occasions' => [
            'tables' => ['occasions', 'occasion_products'],
            'folders' => ['occasions'],
            'attachable_type' => 'Modules\\CatalogManagement\\app\\Models\\Occasion',
        ],
        'blog_categories' => [
            'tables' => ['blog_categories'],
            'folders' => [],
            'attachable_type' => 'Modules\\SystemSetting\\app\\Models\\BlogCategory',
        ],
        'blogs' => [
            'tables' => ['blogs'],
            'folders' => ['blog-images'],
            'attachable_type' => 'Modules\\SystemSetting\\app\\Models\\Blog',
        ],
        'ads_positions' => [
            'tables' => ['ads_positions'],
            'folders' => [],
            'attachable_type' => null,
        ],
        'ads' => [
            'tables' => ['ads'],
            'folders' => ['ads-images'],
            'attachable_type' => 'Modules\\SystemSetting\\app\\Models\\Ad',
        ],
        'cities' => [
            'tables' => ['shipping_cities', 'shipping_categories', 'shippings', 'regions', 'cities'],
            'folders' => ['city-images'],
            'attachable_type' => 'Modules\\AreaSettings\\app\\Models\\City',
        ],
        'products' => [
            'tables' => ['vendor_product_variant_stocks', 'vendor_product_variants', 'vendor_products', 'products', 'product_variants'],
            'folders' => ['product-images'],
            'attachable_type' => 'Modules\\CatalogManagement\\app\\Models\\Product',
        ],
        'users' => [
            'tables' => ['user_points', 'customer_addresses', 'customer_fcm_tokens', 'customers'],
            'folders' => ['customer-images'],
            'attachable_type' => null,
        ],
        'bundle_categories' => [
            'tables' => ['bundle_categories'],
            'folders' => ['bundles_categories-images'],
            'attachable_type' => 'Modules\\CatalogManagement\\app\\Models\\BundleCategory',
        ],
        'bundles' => [
            'tables' => ['bundle_products', 'bundles'],
            'folders' => ['bundles-images'],
            'attachable_type' => 'Modules\\CatalogManagement\\app\\Models\\Bundle',
        ],
        'admins' => [
            'tables' => [],
            'folders' => [],
            'truncate_admin_users' => true,
        ],
    ];
    
    /**
     * Inject data from external API
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function inject(Request $request)
    {
        return "test";
        // Disable Telescope for this request (it consumes too much memory)
        if (class_exists(\Laravel\Telescope\Telescope::class)) {
            \Laravel\Telescope\Telescope::stopRecording();
        }
        
        // Increase memory limit and execution time for large imports
        ini_set('memory_limit', '2048M'); // Increase to 2GB
        ini_set('max_execution_time', '7200'); // 60 minutes (1 hour)
        set_time_limit(3600); // Also set via set_time_limit
        
        // Disable query logging to save memory
        DB::connection()->disableQueryLog();
        
        $include = $request->get('include', 'departments');
        $truncate = $request->get('truncate', '0') === '1';
        $limitPages = $request->get('limit_pages') ? (int) $request->get('limit_pages') : null;
        $startPage = (int) $request->get('page', 1);
        
        $truncateResult = null;
        $page = $startPage;
        $lastPage = 1;
        $totalFetched = 0;
        $combinedResult = [
            'type' => $include,
            'injected' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        try {
            if ($truncate) {
                $truncateResult = $this->truncateBeforeInject($include);
                Log::info("Truncating data for {$include}");
                Log::info("Truncate complete for {$include}", $truncateResult ?? []);
            } elseif ($truncate) {
                Log::info("Truncate already done for {$include}, skipping");
            }

            // Fetch and process page by page
            while (true) {
                // Map include type to API parameter
                // For main_categories and sub_categories, we need to request 'categories' from the API
                $apiInclude = ($include === 'main_categories' || $include === 'sub_categories') 
                    ? 'categories' 
                    : $include;
                
                $response = Http::withOptions(['verify' => false])
                    ->get("{$this->sourceBaseUrl}/api/inject-products", [
                        'include' => $apiInclude,
                        'page' => $page,
                    ]);
                if (!$response->successful()) {
                    $combinedResult['errors'][] = "Failed to fetch page {$page} from source";
                    $page++;
                    if ($page > $lastPage) break;
                    continue;
                }

                $data = $response->json();

                if (!isset($data['status']) || !$data['status']) {
                    $combinedResult['errors'][] = "Page {$page}: " . ($data['message'] ?? 'Source returned error');
                    $page++;
                    if ($page > $lastPage) break;
                    continue;
                }
                
                // Handle categories - can be split into main_categories or sub_categories
                if ($include === 'categories' || $include === 'main_categories' || $include === 'sub_categories') {
                    $mainCats = $data['data']['main_categories'] ?? null;
                    $subCats = $data['data']['sub_categories'] ?? null;
                    
                    // If requesting only main_categories
                    if ($include === 'main_categories') {
                        $pageData = [
                            'main_categories' => ['data' => $mainCats['data'] ?? []],
                        ];
                        $lastPage = $mainCats['last_page'] ?? 1;
                        $totalFetched += count($mainCats['data'] ?? []);
                        $pageResult = $this->injectCategories($pageData);
                    }
                    // If requesting only sub_categories
                    elseif ($include === 'sub_categories') {
                        $pageData = [
                            'sub_categories' => ['data' => $subCats['data'] ?? []],
                        ];
                        $lastPage = $subCats['last_page'] ?? 1;
                        $totalFetched += count($subCats['data'] ?? []);
                        $pageResult = $this->injectSubCategories($pageData);
                    }
                    // If requesting both (legacy 'categories')
                    else {
                        $pageData = [
                            'main_categories' => ['data' => $mainCats['data'] ?? []],
                            'sub_categories' => ['data' => $subCats['data'] ?? []],
                        ];
                        $lastPage = max($lastPage, $mainCats['last_page'] ?? 1, $subCats['last_page'] ?? 1);
                        $totalFetched += count($mainCats['data'] ?? []) + count($subCats['data'] ?? []);
                        $pageResult = $this->injectData($pageData, $include);
                    }
                    
                } else {
                    $paginatedData = $data['data'][$include] ?? null;
                    
                    if ($paginatedData && isset($paginatedData['data'])) {
                        $pageData = [$include => ['data' => $paginatedData['data']]];
                        $lastPage = $paginatedData['last_page'] ?? 1;
                        $totalFetched += count($paginatedData['data']);
                        
                        $pageResult = $this->injectData($pageData, $include);
                    } else {
                        $page++;
                        if ($page > $lastPage) break;
                        continue;
                    }
                }

                // Merge results
                $combinedResult['injected'] += $pageResult['injected'] ?? 0;
                $combinedResult['updated'] += $pageResult['updated'] ?? 0;
                $combinedResult['skipped'] += $pageResult['skipped'] ?? 0;
                if (!empty($pageResult['errors'])) {
                    $combinedResult['errors'] = array_merge($combinedResult['errors'], $pageResult['errors']);
                }
                foreach ($pageResult as $key => $value) {
                    if (!in_array($key, ['type', 'injected', 'updated', 'skipped', 'errors']) && is_numeric($value)) {
                        $combinedResult[$key] = ($combinedResult[$key] ?? 0) + $value;
                    }
                }

                Log::info("Processed page {$page}/{$lastPage} for {$include}");
                
                // Force memory cleanup after each page
                unset($response, $data, $pageData, $pageResult);
                gc_collect_cycles();
                
                $page++;

                // Stop conditions
                if ($page > $lastPage) {
                    break;
                }
                // Optional: stop after processing a limited number of pages
                if ($limitPages !== null && ($page - $startPage) >= $limitPages) {
                    break;
                }
            }

            // Limit errors in response
            if (count($combinedResult['errors']) > 50) {
                $combinedResult['errors'] = array_slice($combinedResult['errors'], 0, 50);
                $combinedResult['errors'][] = '... and more errors (truncated)';
            }

            return response()->json([
                'status' => true,
                'message' => 'Data injected successfully',
                'total_fetched' => $totalFetched,
                'pages_processed' => $page - $startPage,
                'last_page' => $lastPage,
                'truncated' => $truncateResult,
                'result' => $combinedResult,
            ]);

        } catch (\Exception $e) {
            Log::error('Inject data error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Inject data into database based on type
     */
    protected function injectData(array $data, string $include): array
    {
        return match ($include) {
            'departments' => $this->injectDepartments($data),
            'categories' => $this->injectAllCategories($data),
            'main_categories' => $this->injectCategories($data),
            'sub_categories' => $this->injectSubCategories($data),
            'variant_keys' => $this->injectVariantKeys($data),
            'variants' => $this->injectVariants($data),
            'brands' => $this->injectBrands($data),
            'taxes' => $this->injectTaxes($data),
            'blog_categories' => $this->injectBlogCategories($data),
            'blogs' => $this->injectBlogs($data),
            'ads_positions' => $this->injectAdsPositions($data),
            'ads' => $this->injectAds($data),
            'cities' => $this->injectCities($data),
            'products' => $this->injectProducts($data),
            'users' => $this->injectCustomers($data),
            'occasions' => $this->injectOccasions($data),
            'bundle_categories' => $this->injectBundleCategories($data),
            'bundles' => $this->injectBundles($data),
            'admins' => $this->injectAdmins($data),
            default => ['message' => "Unknown include type: {$include}"],
        };
    }

    /**
     * Truncate existing data before injection
     */
    protected function truncateBeforeInject(string $include): ?array
    {
        if (!isset($this->truncateConfig[$include])) {
            return null;
        }

        $config = $this->truncateConfig[$include];
        $deletedRecords = 0;
        $deletedFiles = 0;
        $deletedAttachments = 0;
        $deletedUsers = 0;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Special handling for admin users truncation
            if (!empty($config['truncate_admin_users'])) {
                // Get admin user type ID (user_type_id = 2)
                $adminUserTypeId = 2;
                
                // Get admin user IDs
                $adminUserIds = DB::table('users')
                    ->where('user_type_id', $adminUserTypeId)
                    ->pluck('id');
                
                Log::info("Truncating admin users", [
                    'admin_user_type_id' => $adminUserTypeId,
                    'admin_count' => $adminUserIds->count(),
                ]);
                
                if ($adminUserIds->count() > 0) {
                    // Delete user roles
                    DB::table('user_role')->whereIn('user_id', $adminUserIds)->delete();
                    
                    // Delete user translations
                    DB::table('translations')
                        ->where('translatable_type', 'App\\Models\\User')
                        ->whereIn('translatable_id', $adminUserIds)
                        ->delete();
                    
                    // Delete user attachments
                    $deletedAttachments += DB::table('attachments')
                        ->where('attachable_type', 'App\\Models\\User')
                        ->whereIn('attachable_id', $adminUserIds)
                        ->delete();
                    
                    // Delete admin users
                    $deletedUsers = DB::table('users')
                        ->where('user_type_id', $adminUserTypeId)
                        ->delete();
                    
                    Log::info("Admin users truncated", [
                        'deleted_users' => $deletedUsers,
                        'deleted_attachments' => $deletedAttachments,
                    ]);
                }
            }
            
            // Delete vendor users if configured
            if (!empty($config['delete_vendor_users'])) {
                $vendorUserTypeIds = [UserType::VENDOR_TYPE, UserType::VENDOR_USER_TYPE];
                
                $vendorUserIds = DB::table('users')
                    ->whereIn('user_type_id', $vendorUserTypeIds)
                    ->pluck('id');
                
                if ($vendorUserIds->count() > 0) {
                    DB::table('user_role')->whereIn('user_id', $vendorUserIds)->delete();
                    DB::table('translations')
                        ->where('translatable_type', 'App\\Models\\User')
                        ->whereIn('translatable_id', $vendorUserIds)
                        ->delete();
                }
                
                $deletedUsers = DB::table('users')
                    ->whereIn('user_type_id', $vendorUserTypeIds)
                    ->delete();
            }

            // Delete attachments
            if (!empty($config['attachable_type'])) {
                $deletedAttachments += DB::table('attachments')
                    ->where('attachable_type', $config['attachable_type'])
                    ->delete();
                DB::table('translations')
                    ->where('translatable_type', $config['attachable_type'])
                    ->delete();
            }

            // Delete attachments for multiple types
            if (!empty($config['attachable_types'])) {
                foreach ($config['attachable_types'] as $type) {
                    $deletedAttachments += DB::table('attachments')
                        ->where('attachable_type', $type)
                        ->delete();
                    DB::table('translations')
                        ->where('translatable_type', $type)
                        ->delete();
                }
            }

            // Truncate tables
            foreach ($config['tables'] as $table) {
                if (Schema::hasTable($table)) {
                    $count = DB::table($table)->count();
                    $deletedRecords += $count;
                    DB::table($table)->truncate();
                }
            }

            // Delete storage folders
            foreach ($config['folders'] as $folder) {
                if (Storage::disk('public')->exists($folder)) {
                    $files = Storage::disk('public')->allFiles($folder);
                    $deletedFiles += count($files);
                    Storage::disk('public')->deleteDirectory($folder);
                }
            }

        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        return [
            'records_deleted' => $deletedRecords,
            'files_deleted' => $deletedFiles,
            'attachments_deleted' => $deletedAttachments,
            'users_deleted' => $deletedUsers,
        ];
    }

    /**
     * Inject both main categories and sub categories
     */
    protected function injectAllCategories(array $data): array
    {
        $mainResult = $this->injectCategories($data);
        $subResult = $this->injectSubCategories($data);

        return [
            'main_categories' => $mainResult,
            'sub_categories' => $subResult,
        ];
    }

    /**
     * Inject departments into database
     */
    protected function injectDepartments(array $data): array
    {
        $items = $data['departments']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $errors = [];

        foreach ($items as $item) {
            try {// Check if department exists by ID (keep same ID from source)
                $department = Department::where('id', $item['id'])
                    ->first();

                if ($department) {
                    // Update existing
                    $department->update([
                        'slug' => $item['slug_en'],
                        'active' => $item['status'] == '1',
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID from source
                    $department = new Department();
                    $department->id = $item['id'];
                    $department->slug = $item['slug_en'];
                    $department->active = $item['status'] == '1';
                    $department->created_at = $this->parseDate($item['created_at'] ?? null);
                    $department->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $department->save();
                    $injected++;
                }

                // Set translations
                $department->setTranslation('name', 'en', $item['title_en']);
                $department->setTranslation('name', 'ar', $item['title_ar']);
                $department->save();

                // Download and attach image
                if (!empty($item['image'])) {
                    $this->attachImage($department, $item['image'], 'image');
                }

                // Download and attach icon
                if (!empty($item['icon'])) {
                    $this->attachImage($department, $item['icon'], 'icon');
                }} catch (\Exception $e) {$errors[] = "Department {$item['title_en']} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting department: " . $e->getMessage());
            }
        }

        return [
            'type' => 'departments',
            'injected' => $injected,
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    /**
     * Inject categories into database
     */
    protected function injectCategories(array $data): array
    {
        $items = $data['main_categories']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $errors = [];

        foreach ($items as $item) {
            try {// Check if category exists by ID
                $category = Category::where('id', $item['id'])
                    ->first();

                if ($category) {
                    // Update existing
                    $category->update([
                        'slug' => $item['slug_en'] ?? $item['slug'] ?? null,
                        'department_id' => $item['department_id'] ?? null,
                        'active' => ($item['status'] ?? '1') == '1',
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID
                    $category = new Category();
                    $category->id = $item['id'];
                    $category->slug = $item['slug_en'] ?? $item['slug'] ?? null;
                    $category->department_id = $item['department_id'] ?? null;
                    $category->active = ($item['status'] ?? '1') == '1';
                    $category->created_at = $this->parseDate($item['created_at'] ?? null);
                    $category->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $category->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['title_en'])) {
                    $category->setTranslation('name', 'en', $item['title_en']);
                }
                if (!empty($item['title_ar'])) {
                    $category->setTranslation('name', 'ar', $item['title_ar']);
                }

                if (!empty($item['summary_en'])) {
                    $category->setTranslation('description', 'en', $item['summary_en']);
                }
                if (!empty($item['summary_ar'])) {
                    $category->setTranslation('description', 'ar', $item['summary_ar']);
                }
                
                $category->save();

                // Download and attach image
                if (!empty($item['image'])) {
                    $this->attachImage($category, $item['image'], 'image');
                }

                // Download and attach icon
                if (!empty($item['icon'])) {
                    $this->attachImage($category, $item['icon'], 'icon');
                }} catch (\Exception $e) {$titleEn = $item['title_en'] ?? $item['id'];
                $errors[] = "Category {$titleEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting category: " . $e->getMessage());
            }
        }

        return [
            'type' => 'main_categories',
            'injected' => $injected,
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    /**
     * Inject sub-categories into database
     */
    protected function injectSubCategories(array $data): array
    {
        $items = $data['sub_categories']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
        
        $totalItems = count($items);
        Log::info("=== Starting SubCategory Injection ===", [
            'total_items' => $totalItems
        ]);

        foreach ($items as $index => $item) {
            $itemNumber = $index + 1;
            $subCategoryId = $item['id'] ?? 'unknown';
            $titleEn = $item['title_en'] ?? 'N/A';
            
            Log::info("Processing SubCategory {$itemNumber}/{$totalItems}", [
                'id' => $subCategoryId,
                'title_en' => $titleEn,
            ]);
            
            try {
                // Get category_id - try different possible keys
                $categoryId = $item['category_id'] ?? $item['main_category_id'] ?? $item['parent_id'] ?? null;
                
                Log::info("SubCategory {$subCategoryId}: Checking parent category", [
                    'category_id' => $categoryId,
                ]);
                
                // Skip if no category_id (can't create orphan subcategory)
                if (!$categoryId) {
                    $skipped++;
                    Log::warning("SubCategory {$subCategoryId}: SKIPPED - No category_id found", [
                        'title' => $titleEn,
                        'item_data' => $item,
                    ]);
                    continue;
                }

                // Validate that parent category exists
                if (!Category::where('id', $categoryId)->exists()) {
                    $skipped++;
                    $error = "SubCategory {$titleEn} (ID: {$subCategoryId}): Parent category {$categoryId} not found";
                    $errors[] = $error;
                    Log::warning("SubCategory {$subCategoryId}: SKIPPED - Parent category not found", [
                        'title' => $titleEn,
                        'category_id' => $categoryId,
                    ]);
                    continue;
                }

                // Check if subcategory exists by ID
                $subCategory = SubCategory::where('id', $subCategoryId)->first();

                if ($subCategory) {
                    // Update existing
                    $subCategory->update([
                        'slug' => $item['slug_en'] ?? $item['slug'] ?? null,
                        'category_id' => $categoryId,
                        'active' => ($item['status'] ?? '1') == '1',
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                    Log::info("SubCategory {$subCategoryId}: UPDATED", [
                        'title' => $titleEn,
                        'category_id' => $categoryId,
                    ]);
                } else {
                    // Create new with same ID
                    $subCategory = new SubCategory();
                    $subCategory->id = $subCategoryId;
                    $subCategory->slug = $item['slug_en'] ?? $item['slug'] ?? null;
                    $subCategory->category_id = $categoryId;
                    $subCategory->active = ($item['status'] ?? '1') == '1';
                    $subCategory->created_at = $this->parseDate($item['created_at'] ?? null);
                    $subCategory->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $subCategory->save();
                    $injected++;
                    Log::info("SubCategory {$subCategoryId}: CREATED", [
                        'title' => $titleEn,
                        'category_id' => $categoryId,
                    ]);
                }

                // Set translations
                if (!empty($item['title_en'])) {
                    $subCategory->setTranslation('name', 'en', $item['title_en']);
                }
                if (!empty($item['title_ar'])) {
                    $subCategory->setTranslation('name', 'ar', $item['title_ar']);
                }
                $subCategory->save();

                // Download and attach image
                if (!empty($item['image'])) {
                    $this->attachImage($subCategory, $item['image'], 'image');
                }

                // Download and attach icon
                if (!empty($item['icon'])) {
                    $this->attachImage($subCategory, $item['icon'], 'icon');
                }
                
                Log::info("SubCategory {$subCategoryId}: SUCCESS", [
                    'title' => $titleEn,
                ]);
                
            } catch (\Exception $e) {
                $error = "SubCategory {$titleEn} (ID: {$subCategoryId}): " . $e->getMessage();
                $errors[] = $error;
                Log::error("SubCategory {$subCategoryId}: ERROR", [
                    'title' => $titleEn,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
        
        Log::info("=== SubCategory Injection Complete ===", [
            'total_items' => $totalItems,
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors_count' => count($errors),
            'processed' => $injected + $updated + $skipped,
            'missing' => $totalItems - ($injected + $updated + $skipped),
        ]);

        return [
            'type' => 'sub_categories',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Inject variant keys into database
     */
    protected function injectVariantKeys(array $data): array
    {
        $items = $data['variant_keys']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $errors = [];

        // First pass: create/update all keys without parent_key_id to avoid FK issues
        // Second pass: update parent_key_id relationships
        
        // Sort items: parent keys first (null parent_key_id), then children
        usort($items, function($a, $b) {
            $aParent = $a['parent_key_id'] ?? null;
            $bParent = $b['parent_key_id'] ?? null;
            if ($aParent === null && $bParent !== null) return -1;
            if ($aParent !== null && $bParent === null) return 1;
            return 0;
        });

        foreach ($items as $item) {
            try {// Check if variant key exists by ID
                $variantKey = VariantConfigurationKey::where('id', $item['id'])->first();

                if ($variantKey) {
                    // Update existing
                    $variantKey->update([
                        'parent_key_id' => $item['parent_key_id'] ?? null,
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID
                    $variantKey = new VariantConfigurationKey();
                    $variantKey->id = $item['id'];
                    $variantKey->parent_key_id = $item['parent_key_id'] ?? null;
                    $variantKey->created_at = $this->parseDate($item['created_at'] ?? null);
                    $variantKey->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $variantKey->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['name_en'])) {
                    $variantKey->setTranslation('name', 'en', $item['name_en']);
                }
                if (!empty($item['name_ar'])) {
                    $variantKey->setTranslation('name', 'ar', $item['name_ar']);
                }
                $variantKey->save();} catch (\Exception $e) {$nameEn = $item['name_en'] ?? $item['id'];
                $errors[] = "VariantKey {$nameEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting variant key: " . $e->getMessage());
            }
        }

        return [
            'type' => 'variant_keys',
            'injected' => $injected,
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    /**
     * Inject variants into database
     */
    protected function injectVariants(array $data): array
    {
        $items = $data['variants']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        // Store parent_id mappings for second pass
        $parentMappings = [];

        // First pass: Insert/update all variants WITHOUT parent_id to avoid FK issues
        foreach ($items as $item) {
            // Skip empty items
            if (empty($item) || !isset($item['id'])) {
                $skipped++;
                continue;
            }

            try {
                // Store parent_id for second pass
                if (!empty($item['parent_id'])) {
                    $parentMappings[$item['id']] = $item['parent_id'];
                }

                // Check if variant exists by ID
                $variant = VariantsConfiguration::where('id', $item['id'])->first();

                if ($variant) {
                    // Update existing (without parent_id for now)
                    $variant->update([
                        'key_id' => $item['key_id'] ?? null,
                        'value' => $item['color'] ?? null,
                        'type' => !empty($item['color']) ? 'color' : 'text',
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID (without parent_id for now)
                    $variant = new VariantsConfiguration();
                    $variant->id = $item['id'];
                    $variant->key_id = $item['key_id'] ?? null;
                    $variant->parent_id = null; // Will be set in second pass
                    $variant->value = $item['color'] ?? null;
                    $variant->type = !empty($item['color']) ? 'color' : 'text';
                    $variant->created_at = $this->parseDate($item['created_at'] ?? null);
                    $variant->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $variant->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['name_en'])) {
                    $variant->setTranslation('name', 'en', $item['name_en']);
                }
                if (!empty($item['name_ar'])) {
                    $variant->setTranslation('name', 'ar', $item['name_ar']);
                }
                $variant->save();

            } catch (\Exception $e) {
                $nameEn = $item['name_en'] ?? $item['id'];
                $errors[] = "Variant {$nameEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting variant: " . $e->getMessage());
            }
        }

        // Second pass: Update parent_id for variants that have parents
        foreach ($parentMappings as $variantId => $parentId) {
            try {
                // Only update if parent exists
                if (VariantsConfiguration::where('id', $parentId)->exists()) {
                    VariantsConfiguration::where('id', $variantId)->update(['parent_id' => $parentId]);
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to set parent_id {$parentId} for variant {$variantId}: " . $e->getMessage();
            }
        }

        return [
            'type' => 'variants',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Inject brands into database (also creates vendor and user for each brand)
     */
    protected function injectBrands(array $data): array
    {
        $items = $data['brands']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $errors = [];

        foreach ($items as $item) {
            try {// Check if brand exists by ID
                $brand = Brand::where('id', $item['id'])->first();

                if ($brand) {
                    // Update existing brand
                    $brand->update([
                        'slug' => $item['slug_en'] ?? null,
                        'facebook_url' => $item['facebook'] ?? null,
                        'twitter_url' => $item['x'] ?? null,
                        'linkedin_url' => $item['linkedin'] ?? null,
                        'pinterest_url' => $item['pinterest'] ?? null,
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new brand with same ID
                    $brand = new Brand();
                    $brand->id = $item['id'];
                    $brand->slug = $item['slug_en'] ?? null;
                    $brand->facebook_url = $item['facebook'] ?? null;
                    $brand->twitter_url = $item['x'] ?? null;
                    $brand->linkedin_url = $item['linkedin'] ?? null;
                    $brand->pinterest_url = $item['pinterest'] ?? null;
                    $brand->active = true;
                    $brand->created_at = $this->parseDate($item['created_at'] ?? null);
                    $brand->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $brand->save();
                    $injected++;
                }

                // Set brand translations
                if (!empty($item['name_en'])) {
                    $brand->setTranslation('name', 'en', $item['name_en']);
                }
                if (!empty($item['name_ar'])) {
                    $brand->setTranslation('name', 'ar', $item['name_ar']);
                }
                if (!empty($item['describtion_en'])) {
                    $brand->setTranslation('description', 'en', $item['describtion_en']);
                }
                if (!empty($item['describtion_ar'])) {
                    $brand->setTranslation('description', 'ar', $item['describtion_ar']);
                }
                $brand->save();

                // Download and attach logo
                if (!empty($item['logo'])) {
                    $this->attachImage($brand, $item['logo'], 'logo');
                }

                // Download and attach cover
                if (!empty($item['cover'])) {
                    $this->attachImage($brand, $item['cover'], 'cover');
                }

                // Create or update vendor for this brand (same ID)
                $this->createOrUpdateVendorForBrand($item);} catch (\Exception $e) {$nameEn = $item['name_en'] ?? $item['id'];
                $errors[] = "Brand {$nameEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting brand: " . $e->getMessage());
            }
        }

        return [
            'type' => 'brands',
            'injected' => $injected,
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    /**
     * Create or update vendor and user for a brand
     */
    protected function createOrUpdateVendorForBrand(array $item): void
    {
        // Temporarily disable vendor observer to prevent notification errors during import
        Vendor::withoutEvents(function () use ($item) {
            $this->createOrUpdateVendorForBrandWithoutEvents($item);
        });
    }
    
    /**
     * Create or update vendor without triggering events
     */
    protected function createOrUpdateVendorForBrandWithoutEvents(array $item): void
    {
        // Check if vendor exists with same ID
        $vendor = Vendor::where('id', $item['id'])->first();
        
        // Generate email from slug
        $email = ($item['slug_en'] ?? 'vendor-' . $item['id']) . '@bnaia.com';

        if (!$vendor) {
            // Create user first
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $user = new User();
                $user->uuid = Str::uuid();
                $user->email = $email;
                $user->password = bcrypt('password123');
                $user->user_type_id = UserType::VENDOR_TYPE;
                $user->active = true;
                $user->block = false;
                $user->save();

                // Set user name translations
                if (!empty($item['name_en'])) {
                    $user->setTranslation('name', 'en', $item['name_en']);
                }
                if (!empty($item['name_ar'])) {
                    $user->setTranslation('name', 'ar', $item['name_ar']);
                }
                $user->save();

                // Assign vendor role to user
                $vendorRole = Role::where('type', Role::VENDOR_ROLE_TYPE)->first();
                if ($vendorRole) {
                    $user->roles()->sync([$vendorRole->id]);
                }

                // Attach logo image to user
                if (!empty($item['logo'])) {
                    $localPath = $this->downloadImage($item['logo']);
                    if ($localPath) {
                        $user->update(['image' => $localPath]);
                    }
                }
            }

            // Create vendor with same ID as brand
            $vendor = new Vendor();
            $vendor->id = $item['id'];
            $vendor->user_id = $user->id;
            $vendor->slug = $item['slug_en'] ?? null;
            $vendor->active = true;
            $vendor->country_id = 1; // Default country (Egypt)
            $vendor->created_at = $this->parseDate($item['created_at'] ?? null);
            $vendor->updated_at = $this->parseDate($item['updated_at'] ?? null);
            $vendor->save();

            // Update user with vendor_id
            $user->update(['vendor_id' => $vendor->id]);
        } else {
            // Update existing vendor
            $vendor->update([
                'slug' => $item['slug_en'] ?? $vendor->slug,
                'created_at' => $this->parseDate($item['created_at'] ?? null),
                'updated_at' => $this->parseDate($item['updated_at'] ?? null),
            ]);

            // Update user image if vendor has a user
            if ($vendor->user && !empty($item['logo'])) {
                $localPath = $this->downloadImage($item['logo']);
                if ($localPath) {
                    $vendor->user->update(['image' => $localPath]);
                }
            }
        }

        $departments = Department::pluck('id');
        $vendor->departments()->sync($departments);
        
        // Assign all regions to vendor
        $regions = \Modules\AreaSettings\app\Models\Region::pluck('id');
        $vendor->regions()->sync($regions);
        
        // Set vendor translations
        if (!empty($item['name_en'])) {
            $vendor->setTranslation('name', 'en', $item['name_en']);
        }
        if (!empty($item['name_ar'])) {
            $vendor->setTranslation('name', 'ar', $item['name_ar']);
        }
        if (!empty($item['describtion_en'])) {
            $vendor->setTranslation('description', 'en', $item['describtion_en']);
        }
        if (!empty($item['describtion_ar'])) {
            $vendor->setTranslation('description', 'ar', $item['describtion_ar']);
        }
        $vendor->save();

        // Attach logo to vendor
        if (!empty($item['logo'])) {
            $this->attachImage($vendor, $item['logo'], 'logo');
        }

        // Attach cover as banner to vendor
        if (!empty($item['cover'])) {
            $this->attachImage($vendor, $item['cover'], 'banner');
        }
    }

    /**
     * Inject taxes into database
     */
    protected function injectTaxes(array $data): array
    {
        $items = $data['taxes']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($items as $item) {
            // Skip empty items
            if (empty($item) || !isset($item['id'])) {
                $skipped++;
                continue;
            }

            try {// Check if tax exists by ID
                $tax = Tax::where('id', $item['id'])->first();

                if ($tax) {
                    // Update existing
                    $tax->update([
                        'percentage' => $item['precentage'] ?? 0,
                        'is_active' => ($item['status'] ?? '0') == '1',
                        'created_at' => $item['created_at'] ? \Carbon\Carbon::parse($item['created_at']) : null,
                        'updated_at' => $item['updated_at'] ? \Carbon\Carbon::parse($item['updated_at']) : null,
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID
                    $tax = new Tax();
                    $tax->id = $item['id'];
                    $tax->percentage = $item['precentage'] ?? 0;
                    $tax->is_active = ($item['status'] ?? '0') == '1';
                    $tax->created_at = $item['created_at'] ? \Carbon\Carbon::parse($item['created_at']) : null;
                    $tax->updated_at = $item['updated_at'] ? \Carbon\Carbon::parse($item['updated_at']) : null;
                    $tax->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['title_en'])) {
                    $tax->setTranslation('name', 'en', $item['title_en']);
                }
                if (!empty($item['title_ar'])) {
                    $tax->setTranslation('name', 'ar', $item['title_ar']);
                }
                $tax->save();} catch (\Exception $e) {$titleEn = $item['title_en'] ?? $item['id'];
                $errors[] = "Tax {$titleEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting tax: " . $e->getMessage());
            }
        }

        return [
            'type' => 'taxes',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Inject occasions into database
     */
    protected function injectOccasions(array $data): array
    {
        $items = $data['occasions']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($items as $item) {
            // Skip empty items
            if (empty($item) || !isset($item['id'])) {
                $skipped++;
                continue;
            }

            try {// Check if occasion exists by ID
                $occasion = Occasion::where('id', $item['id'])->first();

                // Parse dates
                $startDate = $this->parseDate($item['start_date'] ?? null);
                $endDate = $this->parseDate($item['end_date'] ?? null);

                if ($occasion) {
                    // Update existing
                    $occasion->update([
                        'slug' => $item['slug_en'] ?? null,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'is_active' => ($item['status'] ?? '0') == '1',
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID
                    $occasion = new Occasion();
                    $occasion->id = $item['id'];
                    $occasion->slug = $item['slug_en'] ?? null;
                    $occasion->start_date = $startDate;
                    $occasion->end_date = $endDate;
                    $occasion->is_active = ($item['status'] ?? '0') == '1';
                    $occasion->created_at = $this->parseDate($item['created_at'] ?? null);
                    $occasion->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $occasion->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['name_en'])) {
                    $occasion->setTranslation('name', 'en', $item['name_en']);
                }
                if (!empty($item['name_ar'])) {
                    $occasion->setTranslation('name', 'ar', $item['name_ar']);
                }
                if (!empty($item['title_en'])) {
                    $occasion->setTranslation('title', 'en', $item['title_en']);
                }
                if (!empty($item['title_ar'])) {
                    $occasion->setTranslation('title', 'ar', $item['title_ar']);
                }
                if (!empty($item['subtitle_en'])) {
                    $occasion->setTranslation('subtitle', 'en', $item['subtitle_en']);
                }
                if (!empty($item['subtitle_ar'])) {
                    $occasion->setTranslation('subtitle', 'ar', $item['subtitle_ar']);
                }
                if (!empty($item['description_en'])) {
                    $occasion->setTranslation('description', 'en', $item['description_en']);
                }
                if (!empty($item['description_ar'])) {
                    $occasion->setTranslation('description', 'ar', $item['description_ar']);
                }
                $occasion->save();

                // Download and attach image
                if (!empty($item['image'])) {
                    $this->attachImage($occasion, $item['image'], 'image');
                }

                // Inject occasion products
                if (!empty($item['occasion_products'])) {
                    // Clear existing occasion products for this occasion
                    OccasionProduct::where('occasion_id', $occasion->id)->delete();

                    foreach ($item['occasion_products'] as $op) {
                        try {
                            // Get the product SKU from nested data
                            $productSku = $op['product_size_color']['product']['sku'] ?? null;
                            $variantSku = $op['product_size_color']['sku'] ?? null;

                            if (!$productSku) {
                                continue;
                            }

                            // Find vendor product by SKU
                            $vendorProduct = VendorProduct::where('sku', $productSku)->first();
                            if (!$vendorProduct) {
                                // Try finding by variant SKU as product SKU
                                $vendorProduct = VendorProduct::where('sku', $variantSku)->first();
                            }

                            if (!$vendorProduct) {
                                $errors[] = "Occasion product: VendorProduct not found for SKU: {$productSku}";
                                continue;
                            }

                            // Find vendor product variant by SKU (if different from product SKU)
                            $vendorProductVariantId = null;
                            if ($variantSku && $variantSku !== $productSku) {
                                $vendorProductVariant = VendorProductVariant::where('sku', $variantSku)
                                    ->where('vendor_product_id', $vendorProduct->id)
                                    ->first();
                                $vendorProductVariantId = $vendorProductVariant?->id;
                            }

                            // Create occasion product
                            OccasionProduct::create([
                                'country_id' => $occasion->country_id,
                                'occasion_id' => $occasion->id,
                                'vendor_product_variant_id' => $vendorProductVariantId ?? $vendorProduct->variants()->first()?->id,
                                'special_price' => $op['special_price'] ?? null,
                                'position' => $op['position'] ?? null,
                            ]);
                        } catch (\Exception $e) {
                            $errors[] = "Occasion product error: " . $e->getMessage();
                        }
                    }
                }
            } catch (\Exception $e) {
                $nameEn = $item['name_en'] ?? $item['id'];
                $errors[] = "Occasion {$nameEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting occasion: " . $e->getMessage());
            }
        }

        return [
            'type' => 'occasions',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Inject blog categories into database
     */
    protected function injectBlogCategories(array $data): array
    {
        $items = $data['blog_categories']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($items as $item) {
            // Skip empty items
            if (empty($item) || !isset($item['id'])) {
                $skipped++;
                continue;
            }

            try {// Check if blog category exists by ID
                $blogCategory = BlogCategory::where('id', $item['id'])->first();

                if ($blogCategory) {
                    // Update existing
                    $blogCategory->update([
                        'slug' => $item['slug_en'] ?? null,
                        'active' => ($item['status'] ?? '0') == '1',
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID
                    $blogCategory = new BlogCategory();
                    $blogCategory->id = $item['id'];
                    $blogCategory->slug = $item['slug_en'] ?? null;
                    $blogCategory->active = ($item['status'] ?? '0') == '1';
                    $blogCategory->created_at = $this->parseDate($item['created_at'] ?? null);
                    $blogCategory->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $blogCategory->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['title_en'])) {
                    $blogCategory->setTranslation('title', 'en', $item['title_en']);
                }
                if (!empty($item['title_ar'])) {
                    $blogCategory->setTranslation('title', 'ar', $item['title_ar']);
                }
                if (!empty($item['description_en'])) {
                    $blogCategory->setTranslation('description', 'en', $item['description_en']);
                }
                if (!empty($item['description_ar'])) {
                    $blogCategory->setTranslation('description', 'ar', $item['description_ar']);
                }

                // Handle meta_keywords from array of objects
                if (!empty($item['meta_keywords']) && is_array($item['meta_keywords'])) {
                    $keywordsEn = [];
                    $keywordsAr = [];
                    
                    foreach ($item['meta_keywords'] as $keyword) {
                        if (isset($keyword['keyword']) && isset($keyword['lang'])) {
                            if ($keyword['lang'] === 'en') {
                                $keywordsEn[] = $keyword['keyword'];
                            } elseif ($keyword['lang'] === 'ar') {
                                $keywordsAr[] = $keyword['keyword'];
                            }
                        }
                    }
                    
                    if (!empty($keywordsEn)) {
                        $blogCategory->setTranslation('meta_keywords', 'en', implode(', ', $keywordsEn));
                    }
                    if (!empty($keywordsAr)) {
                        $blogCategory->setTranslation('meta_keywords', 'ar', implode(', ', $keywordsAr));
                    }
                }

                $blogCategory->save();

                // Download and attach image
                if (!empty($item['image'])) {
                    $this->attachImage($blogCategory, $item['image'], 'image');
                }} catch (\Exception $e) {$titleEn = $item['title_en'] ?? $item['id'];
                $errors[] = "BlogCategory {$titleEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting blog category: " . $e->getMessage());
            }
        }

        return [
            'type' => 'blog_categories',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Inject blogs into database
     */
    protected function injectBlogs(array $data): array
    {
        $items = $data['blogs']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($items as $item) {
            // Skip empty items
            if (empty($item) || !isset($item['id'])) {
                $skipped++;
                continue;
            }

            try {// Check if blog exists by ID
                $blog = Blog::where('id', $item['id'])->first();

                if ($blog) {
                    // Update existing
                    $blog->update([
                        'slug' => $item['slug_en'] ?? $item['slug'] ?? null,
                        'blog_category_id' => $item['blog_category_id'] ?? $item['category_id'] ?? null,
                        'active' => ($item['status'] ?? '0') == '1',
                        'views_count' => $item['views_count'] ?? 0,
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID
                    $blog = new Blog();
                    $blog->id = $item['id'];
                    $blog->slug = $item['slug_en'] ?? $item['slug'] ?? null;
                    $blog->blog_category_id = $item['blog_category_id'] ?? $item['category_id'] ?? null;
                    $blog->active = ($item['status'] ?? '0') == '1';
                    $blog->views_count = $item['views_count'] ?? 0;
                    $blog->created_at = $this->parseDate($item['created_at'] ?? null);
                    $blog->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $blog->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['title_en'])) {
                    $blog->setTranslation('title', 'en', $item['title_en']);
                }
                if (!empty($item['title_ar'])) {
                    $blog->setTranslation('title', 'ar', $item['title_ar']);
                }
                if (!empty($item['content_en'])) {
                    $blog->setTranslation('content', 'en', $item['content_en']);
                }
                if (!empty($item['content_ar'])) {
                    $blog->setTranslation('content', 'ar', $item['content_ar']);
                }
                if (!empty($item['meta_title_en'])) {
                    $blog->setTranslation('meta_title', 'en', $item['meta_title_en']);
                }
                if (!empty($item['meta_title_ar'])) {
                    $blog->setTranslation('meta_title', 'ar', $item['meta_title_ar']);
                }
                if (!empty($item['meta_description_en'])) {
                    $blog->setTranslation('meta_description', 'en', $item['meta_description_en']);
                }
                if (!empty($item['meta_description_ar'])) {
                    $blog->setTranslation('meta_description', 'ar', $item['meta_description_ar']);
                }

                // Handle meta_keywords from array of objects
                if (!empty($item['meta_keywords']) && is_array($item['meta_keywords'])) {
                    $keywordsEn = [];
                    $keywordsAr = [];
                    
                    foreach ($item['meta_keywords'] as $keyword) {
                        if (isset($keyword['keyword']) && isset($keyword['lang'])) {
                            if ($keyword['lang'] === 'en') {
                                $keywordsEn[] = $keyword['keyword'];
                            } elseif ($keyword['lang'] === 'ar') {
                                $keywordsAr[] = $keyword['keyword'];
                            }
                        }
                    }
                    
                    if (!empty($keywordsEn)) {
                        $blog->setTranslation('meta_keywords', 'en', implode(', ', $keywordsEn));
                    }
                    if (!empty($keywordsAr)) {
                        $blog->setTranslation('meta_keywords', 'ar', implode(', ', $keywordsAr));
                    }
                }

                $blog->save();

                // Download and attach image
                if (!empty($item['image'])) {
                    $this->attachImage($blog, $item['image'], 'image');
                }} catch (\Exception $e) {$titleEn = $item['title_en'] ?? $item['id'];
                $errors[] = "Blog {$titleEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting blog: " . $e->getMessage());
            }
        }

        return [
            'type' => 'blogs',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Inject ads positions into database
     */
    protected function injectAdsPositions(array $data): array
    {
        $items = $data['ads_positions']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($items as $item) {
            // Skip empty items
            if (empty($item) || !isset($item['id'])) {
                $skipped++;
                continue;
            }

            try {// Check if ad position exists by ID
                $adPosition = AdPosition::where('id', $item['id'])->first();

                if ($adPosition) {
                    // Update existing
                    $adPosition->update([
                        'position' => $item['position'] ?? null,
                        'width' => $item['width'] ?? null,
                        'height' => $item['height'] ?? null,
                        'device' => $item['device'] ?? 'web',
                        'created_at' => $item['created_at'] ? \Carbon\Carbon::parse($item['created_at']) : null,
                        'updated_at' => $item['updated_at'] ? \Carbon\Carbon::parse($item['updated_at']) : null,
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID
                    $adPosition = new AdPosition();
                    $adPosition->id = $item['id'];
                    $adPosition->position = $item['position'] ?? null;
                    $adPosition->width = $item['width'] ?? null;
                    $adPosition->height = $item['height'] ?? null;
                    $adPosition->device = $item['device'] ?? 'web';
                    $adPosition->created_at = $item['created_at'] ? \Carbon\Carbon::parse($item['created_at']) : null;
                    $adPosition->updated_at = $item['updated_at'] ? \Carbon\Carbon::parse($item['updated_at']) : null;
                    $adPosition->save();
                    $injected++;
                }} catch (\Exception $e) {$errors[] = "AdPosition {$item['position']} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting ad position: " . $e->getMessage());
            }
        }

        return [
            'type' => 'ads_positions',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Inject ads into database
     */
    protected function injectAds(array $data): array
    {
        $items = $data['ads']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($items as $item) {
            // Skip empty items
            if (empty($item) || !isset($item['id'])) {
                $skipped++;
                continue;
            }

            try {// Get the type, width, height from ad_position's device
                $type = null;
                $mobileWidth = null;
                $mobileHeight = null;
                $websiteWidth = null;
                $websiteHeight = null;
                
                if (!empty($item['ad_position_id'])) {
                    $adPosition = AdPosition::find($item['ad_position_id']);
                    if ($adPosition) {
                        // Map device to type: 'web' -> 'website', 'mobile' -> 'mobile'
                        $device = $adPosition->device ?? 'web';
                        $type = [$device === 'web' ? 'website' : 'mobile'];
                        
                        // Set width/height based on device type
                        if ($device === 'mobile') {
                            $mobileWidth = $adPosition->width;
                            $mobileHeight = $adPosition->height;
                        } else {
                            $websiteWidth = $adPosition->width;
                            $websiteHeight = $adPosition->height;
                        }
                    }
                }

                // Check if ad exists by ID
                $ad = Ad::where('id', $item['id'])->first();

                if ($ad) {
                    // Update existing
                    $ad->update([
                        'ad_position_id' => $item['ad_position_id'] ?? null,
                        'type' => $type,
                        'mobile_width' => $mobileWidth,
                        'mobile_height' => $mobileHeight,
                        'website_width' => $websiteWidth,
                        'website_height' => $websiteHeight,
                        'link' => $item['link'] ?? null,
                        'active' => true,
                        'created_at' => $item['created_at'] ? \Carbon\Carbon::parse($item['created_at']) : null,
                        'updated_at' => $item['updated_at'] ? \Carbon\Carbon::parse($item['updated_at']) : null,
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID
                    $ad = new Ad();
                    $ad->id = $item['id'];
                    $ad->ad_position_id = $item['ad_position_id'] ?? null;
                    $ad->type = $type;
                    $ad->mobile_width = $mobileWidth;
                    $ad->mobile_height = $mobileHeight;
                    $ad->website_width = $websiteWidth;
                    $ad->website_height = $websiteHeight;
                    $ad->link = $item['link'] ?? null;
                    $ad->active = true;
                    $ad->created_at = $item['created_at'] ? \Carbon\Carbon::parse($item['created_at']) : null;
                    $ad->updated_at = $item['updated_at'] ? \Carbon\Carbon::parse($item['updated_at']) : null;
                    $ad->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['ads_big_text'])) {
                    $ad->setTranslation('title', 'en', $item['ads_big_text']);
                }
                if (!empty($item['ads_big_text_ar'])) {
                    $ad->setTranslation('title', 'ar', $item['ads_big_text_ar']);
                }
                if (!empty($item['ads_small_text'])) {
                    $ad->setTranslation('subtitle', 'en', $item['ads_small_text']);
                }
                if (!empty($item['ads_small_text_ar'])) {
                    $ad->setTranslation('subtitle', 'ar', $item['ads_small_text_ar']);
                }
                $ad->save();

                // Download and attach image
                if (!empty($item['ads_image'])) {
                    $this->attachImage($ad, $item['ads_image'], 'image');
                }} catch (\Exception $e) {$title = $item['ads_big_text'] ?? $item['id'];
                $errors[] = "Ad {$title} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting ad: " . $e->getMessage());
            }
        }

        return [
            'type' => 'ads',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Inject cities with regions and shipping into database
     */
    protected function injectCities(array $data): array
    {
        $items = $data['cities']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $regionsInjected = 0;
        $shippingsInjected = 0;
        $errors = [];

        // Get Egypt country (use the local Egypt country)
        $egyptCountry = Country::where('code', 'eg')->first();
        if (!$egyptCountry) {
            return [
                'type' => 'cities',
                'error' => 'Egypt country not found in database. Please create it first.',
            ];
        }

        foreach ($items as $item) {
            // Skip empty items
            if (empty($item) || !isset($item['id'])) {
                $skipped++;
                continue;
            }

            try {// Check if city exists by ID
                $city = City::where('id', $item['id'])->first();

                if ($city) {
                    // Update existing
                    $city->update([
                        'slug' => $item['title_en'] ? Str::slug($item['title_en']) : null,
                        'country_id' => $egyptCountry->id,
                        'active' => ($item['status'] ?? '1') == '1',
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID
                    $city = new City();
                    $city->id = $item['id'];
                    $city->slug = $item['title_en'] ? Str::slug($item['title_en']) : null;
                    $city->country_id = $egyptCountry->id;
                    $city->active = ($item['status'] ?? '1') == '1';
                    $city->created_at = $this->parseDate($item['created_at'] ?? null);
                    $city->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $city->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['title_en'])) {
                    $city->setTranslation('name', 'en', $item['title_en']);
                }
                if (!empty($item['title_ar'])) {
                    $city->setTranslation('name', 'ar', $item['title_ar']);
                }
                $city->save();

                // Download and attach image
                if (!empty($item['image'])) {
                    $this->attachImage($city, $item['image'], 'image');
                }

                // Inject regions for this city
                if (!empty($item['regions']) && is_array($item['regions'])) {
                    foreach ($item['regions'] as $regionData) {
                        $regionsInjected += $this->injectRegion($regionData, $city->id);
                    }
                }

                // Inject shipping for this city
                if (!empty($item['shipping']) && is_array($item['shipping'])) {
                    foreach ($item['shipping'] as $shippingData) {
                        $shippingsInjected += $this->injectShipping($shippingData, $city->id, $egyptCountry->id);
                    }
                }} catch (\Exception $e) {$titleEn = $item['title_en'] ?? $item['id'];
                $errors[] = "City {$titleEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting city: " . $e->getMessage());
            }
        }

        return [
            'type' => 'cities',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'regions_injected' => $regionsInjected,
            'shippings_injected' => $shippingsInjected,
            'errors' => $errors,
        ];
    }

    /**
     * Inject a single region
     */
    protected function injectRegion(array $item, int $cityId): int
    {
        if (empty($item) || !isset($item['id'])) {
            return 0;
        }

        try {
            $region = Region::where('id', $item['id'])->first();

            if ($region) {
                $region->update([
                    'slug' => $item['title_en'] ? Str::slug($item['title_en']) : null,
                    'city_id' => $cityId,
                    'active' => ($item['status'] ?? '1') == '1',
                    'created_at' => $this->parseDate($item['created_at'] ?? null),
                    'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                ]);
            } else {
                $region = new Region();
                $region->id = $item['id'];
                $region->slug = $item['title_en'] ? Str::slug($item['title_en']) : null;
                $region->city_id = $cityId;
                $region->active = ($item['status'] ?? '1') == '1';
                $region->created_at = $this->parseDate($item['created_at'] ?? null);
                $region->updated_at = $this->parseDate($item['updated_at'] ?? null);
                $region->save();
            }

            // Set translations
            if (!empty($item['title_en'])) {
                $region->setTranslation('name', 'en', $item['title_en']);
            }
            if (!empty($item['title_ar'])) {
                $region->setTranslation('name', 'ar', $item['title_ar']);
            }
            $region->save();

            // Download and attach image if exists
            if (!empty($item['image'])) {
                $this->attachImage($region, $item['image'], 'image');
            }

            return 1;
        } catch (\Exception $e) {
            Log::error("Error injecting region {$item['id']}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Inject a single shipping record
     */
    protected function injectShipping(array $item, int $cityId, int $countryId): int
    {
        if (empty($item) || !isset($item['id'])) {
            return 0;
        }

        try {
            $shipping = Shipping::where('id', $item['id'])->first();

            if ($shipping) {
                $shipping->update([
                    'cost' => $item['cost'] ?? 0,
                    'active' => ($item['status'] ?? 'active') == 'active',
                    'country_id' => $countryId,
                    'created_at' => $this->parseDate($item['created_at'] ?? null),
                    'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                ]);
            } else {
                $shipping = new Shipping();
                $shipping->id = $item['id'];
                $shipping->cost = $item['cost'] ?? 0;
                $shipping->active = ($item['status'] ?? 'active') == 'active';
                $shipping->country_id = $countryId;
                $shipping->created_at = $this->parseDate($item['created_at'] ?? null);
                $shipping->updated_at = $this->parseDate($item['updated_at'] ?? null);
                $shipping->save();
            }

            // Set translations
            if (!empty($item['title_en'])) {
                $shipping->setTranslation('name', 'en', $item['title_en']);
            }
            if (!empty($item['title_ar'])) {
                $shipping->setTranslation('name', 'ar', $item['title_ar']);
            }
            $shipping->save();

            // Attach city to shipping (many-to-many)
            if (!$shipping->cities()->where('city_id', $cityId)->exists()) {
                $shipping->cities()->attach($cityId);
            }

            // Attach category to shipping if category_id exists
            if (!empty($item['category_id'])) {
                if (!$shipping->categories()->where('type_id', $item['category_id'])->exists()) {
                    $shipping->categories()->attach($item['category_id'], ['type' => 'category']);
                }
            }

            return 1;
        } catch (\Exception $e) {
            Log::error("Error injecting shipping {$item['id']}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Inject products into database
     * brand_id is used as both brand_id and vendor_id
     */
    protected function injectProducts(array $data): array
    {
        $items = $data['products']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $vendorProductsCreated = 0;
        $variantsCreated = 0;
        $stocksCreated = 0;
        $errors = [];

        // Get Egypt country
        $egyptCountry = Country::where('code', 'eg')->first();
        if (!$egyptCountry) {
            return [
                'type' => 'products',
                'error' => 'Egypt country not found in database.',
            ];
        }

        // Get default user for created_by_user_id (first admin user)
        $defaultUser = User::first();
        if (!$defaultUser) {
            return [
                'type' => 'products',
                'error' => 'No user found in database for created_by_user_id.',
            ];
        }

        foreach ($items as $item) {
            // Skip empty items
            if (empty($item) || !isset($item['id'])) {
                $skipped++;
                continue;
            }
            
            // Log the product data for debugging
            Log::info("Processing product ID: {$item['id']}", [
                'size_color_type' => $item['size_color_type'] ?? 'not set',
                'product_size_colors_count' => count($item['product_size_colors'] ?? []),
                'product_size_colors' => array_slice($item['product_size_colors'] ?? [], 0, 3), // Log first 3 variants
            ]);

            try {// Validate foreign key references exist
                $departmentId = $item['department_id'] ?? null;
                $categoryId = $item['main_category_id'] ?? null;
                $subCategoryId = $item['sub_category_id'] ?? null;
                $brandId = $item['brand_id'] ?? null;

                // Check if department exists (required)
                if ($departmentId && !Department::where('id', $departmentId)->exists()) {
                    $skipped++;$errors[] = "Product {$item['id']}: Department {$departmentId} not found";
                    continue;
                }

                // Check if category exists (required)
                if ($categoryId && !Category::where('id', $categoryId)->exists()) {
                    $skipped++;$errors[] = "Product {$item['id']}: Category {$categoryId} not found";
                    continue;
                }

                // Check if sub_category exists (optional - set to null if not found)
                if ($subCategoryId && !SubCategory::where('id', $subCategoryId)->exists()) {
                    $subCategoryId = null;
                }

                // Check if brand/vendor exists (required for vendor_product)
                if ($brandId && !Vendor::where('id', $brandId)->exists()) {
                    $brandId = null; // Will skip vendor_product creation
                }

                // Check if product exists by ID (include soft-deleted)
                $product = Product::withTrashed()->where('id', $item['id'])->first();

                // Map size_color_type to configuration_type enum values
                // Also check if product_size_colors has variant configurations
                $configurationType = 'simple'; // default
                $sizeColorType = $item['size_color_type'] ?? null;
                
                // Try multiple possible keys for variants
                $sizeColors = $item['product_size_colors'] 
                    ?? $item['product_size_color'] 
                    ?? $item['variants'] 
                    ?? $item['product_variants']
                    ?? $item['size_colors']
                    ?? [];
                
                // Log variant detection
                Log::info("Product {$item['id']} variant detection", [
                    'size_color_type' => $sizeColorType,
                    'sizeColors_count' => is_array($sizeColors) ? count($sizeColors) : 0,
                    'sizeColors_is_array' => is_array($sizeColors),
                    'available_keys' => array_keys($item),
                ]);
                
                // Check if any variant has a variant_configuration_id or variants_configuration_id
                $hasVariantConfigs = false;
                if (!empty($sizeColors) && is_array($sizeColors)) {
                    foreach ($sizeColors as $sc) {
                        if (!empty($sc['variants_configuration_id']) || !empty($sc['variant_configuration_id'])) {
                            $hasVariantConfigs = true;
                            break;
                        }
                    }
                }
                
                // Also check if there are multiple variants (more than 1 means it's a variant product)
                $hasMultipleVariants = is_array($sizeColors) && count($sizeColors) > 1;
                
                // Database ENUM values are: 'simple', 'variants' (not 'with_variants')
                if ($hasVariantConfigs || $hasMultipleVariants || $sizeColorType === 'with_variants' || $sizeColorType === 'with_size_color' || $sizeColorType === 'with_size' || $sizeColorType === 'with_color') {
                    $configurationType = 'variants';
                }
                
                Log::info("Product {$item['id']} configuration_type: {$configurationType}", [
                    'hasVariantConfigs' => $hasVariantConfigs,
                    'hasMultipleVariants' => $hasMultipleVariants,
                ]);

                // Prepare product data - only columns that exist in products table
                // Make slug unique if duplicate exists
                $slug = $item['slug_en'] ?? null;
                if ($slug) {
                    $existingProduct = Product::where('slug', $slug)
                        ->where('id', '!=', $item['id'])
                        ->first();
                    if ($existingProduct) {
                        // Append product ID to make it unique
                        $slug = $slug . '-' . $item['id'];
                    }
                }
                
                $productData = [
                    'slug' => $slug,
                    'is_active' => ($item['status'] ?? '0') == '1',
                    'configuration_type' => $configurationType,
                    'type' => 'product', // default type
                    'vendor_id' => $brandId, // brand_id = vendor_id
                    'brand_id' => $brandId,
                    'department_id' => $departmentId,
                    'category_id' => $categoryId,
                    'sub_category_id' => $subCategoryId,
                    'created_by_user_id' => $defaultUser->id,
                    'country_id' => $egyptCountry->id,
                    'created_at' => $this->parseDate($item['created_at'] ?? null),
                    'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                ];

                if ($product) {
                    // Restore if soft-deleted
                    if ($product->trashed()) {
                        $product->restore();
                    }
                    // Update existing
                    $product->update($productData);
                    $updated++;
                } else {
                    // Create new with same ID
                    $product = new Product();
                    $product->id = $item['id'];
                    $product->fill($productData);
                    $product->save();
                    $injected++;
                }

                // Set translations
                $this->setProductTranslations($product, $item);
                $product->save();

                // Download and attach main image
                if (!empty($item['image'])) {
                    $this->attachImage($product, $item['image'], 'main_image');
                }

                // Download and attach additional images
                if (!empty($item['product_images']) && is_array($item['product_images'])) {
                    foreach ($item['product_images'] as $additionalImage) {
                        // Skip empty arrays/objects
                        if (empty($additionalImage) || !is_array($additionalImage)) {
                            continue;
                        }
                        if (!empty($additionalImage['image'])) {
                            $this->attachImage($product, $additionalImage['image'], 'additional_image');
                        }
                    }
                }

                // Create VendorProduct if brand_id exists (brand_id = vendor_id)
                $vendorProduct = null;
                if (!empty($brandId)) {
                    $result = $this->createVendorProduct($product, $item, $egyptCountry->id);
                    $vendorProductsCreated += $result['created'];
                    $vendorProduct = $result['vendor_product'];
                }

                // Create VendorProductVariants from product_size_colors
                $productVariantsCreated = 0; // Track variants for THIS product
                if ($vendorProduct) {
                    $sizeColors = $item['product_size_colors'] ?? $item['product_size_color'] ?? [];
                    
                    if (!empty($sizeColors) && is_array($sizeColors)) {
                        foreach ($sizeColors as $sizeColor) {
                            if (empty($sizeColor) || !isset($sizeColor['id'])) {
                                continue;
                            }
                            
                            // Create ProductVariant (bank product variant)
                            $this->createProductVariant($product, $sizeColor);
                            
                            // Create VendorProductVariant
                            $variantResult = $this->createVendorProductVariant($vendorProduct, $sizeColor, $egyptCountry->id);
                            $productVariantsCreated += $variantResult['variant_created'];
                            $variantsCreated += $variantResult['variant_created'];
                            $stocksCreated += $variantResult['stocks_created'];
                        }
                    }
                    
                    // If no variants were created for THIS product but product has stock, create a default variant
                    if ($productVariantsCreated === 0 && isset($item['stock']) && $item['stock'] > 0) {
                        $defaultVariantData = [
                            'id' => $item['id'] * 10000, // Generate unique ID based on product ID
                            'real_price' => $item['real_price'] ?? $item['price'] ?? 0,
                            'fake_price' => $item['fake_price'] ?? null,
                            'discount' => $item['discount'] ?? 0,
                            'sku' => $item['sku'] ?? null,
                            'stock' => $item['stock'],
                            'variants_configuration_id' => null,
                            'created_at' => $item['created_at'] ?? null,
                            'updated_at' => $item['updated_at'] ?? null,
                        ];
                        $variantResult = $this->createVendorProductVariant($vendorProduct, $defaultVariantData, $egyptCountry->id);
                        $variantsCreated += $variantResult['variant_created'];
                        $stocksCreated += $variantResult['stocks_created'];
                    }
                    
                    // Update product configuration_type based on actual variants created
                    // Database ENUM values are: 'simple', 'variants'
                    $hasVariantConfig = VendorProductVariant::where('vendor_product_id', $vendorProduct->id)
                        ->whereNotNull('variant_configuration_id')
                        ->exists();
                    
                    if ($hasVariantConfig && $product->configuration_type !== 'variants') {
                        $product->configuration_type = 'variants';
                        $product->save();
                    }
                }} catch (\Exception $e) {$titleEn = $item['title_en'] ?? $item['id'];
                $errors[] = "Product {$titleEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting product: " . $e->getMessage());
            }
        }

        return [
            'type' => 'products',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'vendor_products_created' => $vendorProductsCreated,
            'variants_created' => $variantsCreated,
            'stocks_created' => $stocksCreated,
            'errors' => $errors,
        ];
    }

    /**
     * Inject customers (users) into database
     */
    protected function injectCustomers(array $data): array
    {
        $items = $data['users']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $pointsCreated = 0;
        $errors = [];

        foreach ($items as $item) {
            // Skip empty items
            if (empty($item) || !isset($item['id'])) {
                $skipped++;
                continue;
            }

            try {// Check if customer exists by ID or email
                $customer = Customer::where('id', $item['id'])->first();
                if (!$customer) {
                    $customer = Customer::where('email', $item['email'])->first();
                }

                // Split name into first_name and last_name
                $nameParts = explode(' ', $item['name'] ?? '', 2);
                $firstName = $nameParts[0] ?? '';
                $lastName = $nameParts[1] ?? '';

                $cityId = $item['city_id'] ?? null;
                $regionId = $item['region_id'] ?? null;
                // Check if city exists
                if ($cityId && !City::where('id', $cityId)->exists()) {
                    $cityId = null;
                }

                // Check if region exists
                if ($regionId && !Region::where('id', $regionId)->exists()) {
                    $regionId = null;
                }

                $customerData = [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $item['email'] ?? null,
                    'phone' => $item['phone'] ?? null,
                    'gender' => $item['gender'] ?? 'male',
                    'status' => ($item['status'] ?? '1') == '1',
                    'lang' => $item['lang'] ?? 'en',
                    'password' => $item['password'],
                    'email_verified_at' => !empty($item['email_verified_at']) ? $this->parseDate($item['email_verified_at']) : null,
                    'city_id' => $cityId,
                    'region_id' => $regionId,
                    'created_at' => $this->parseDate($item['created_at'] ?? null),
                    'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                ];

                if ($customer) {
                    // Update existing - don't update password
                    $customer->update($customerData);
                    $updated++;
                } else {
                    // Create new with same ID
                    $customer = new Customer();
                    $customer->id = $item['id'];
                    $customer->fill($customerData);
                    $customer->password = 'password123'; // Default password
                    $customer->save();
                    $injected++;
                }

                // Create or update user points
                if (!empty($item['points'])) {
                    $pointsData = $item['points'];
                    $userPoints = UserPoints::where('user_id', $customer->id)->first();

                    if ($userPoints) {
                        $userPoints->update([
                            'total_points' => $pointsData['total_points'] ?? 0,
                            'earned_points' => $pointsData['earned_points'] ?? 0,
                            'redeemed_points' => $pointsData['redeemed_points'] ?? 0,
                            'expired_points' => $pointsData['expired_points'] ?? 0,
                        ]);
                    } else {
                        $newPoints = new UserPoints();
                        if (!empty($pointsData['id'])) {
                            $newPoints->id = $pointsData['id'];
                        }
                        $newPoints->user_id = $customer->id;
                        $newPoints->total_points = $pointsData['total_points'] ?? 0;
                        $newPoints->earned_points = $pointsData['earned_points'] ?? 0;
                        $newPoints->redeemed_points = $pointsData['redeemed_points'] ?? 0;
                        $newPoints->expired_points = $pointsData['expired_points'] ?? 0;
                        $newPoints->adjusted_points = 0;
                        $newPoints->save();
                        $pointsCreated++;
                    }
                }

                // Download and attach image if exists
                if (!empty($item['image'])) {
                    $localPath = $this->downloadImage($item['image']);
                    if ($localPath) {
                        $customer->update(['image' => $localPath]);
                    }
                }

                // Create or update customer addresses
                // API returns 'address' (singular) as array of addresses
                $addressList = $item['address'] ?? $item['addresses'] ?? [];
                if (!empty($addressList) && is_array($addressList)) {
                    foreach ($addressList as $addressData) {
                        if (empty($addressData)) continue;
                        $egyptCountry = Country::where('code', 'eg')->first();
                        $addressCityId = $addressData['city_id'] ?? null;
                        $addressRegionId = $addressData['region_id'] ?? null;
                        $addressSubregionId = $addressData['subregion_id'] ?? null;

                        // Validate foreign keys exist
                        if ($addressCityId && !City::where('id', $addressCityId)->exists()) {
                            $addressCityId = null;
                        }
                        if ($addressRegionId && !Region::where('id', $addressRegionId)->exists()) {
                            $addressRegionId = null;
                        }
                        if ($addressSubregionId && !\Modules\AreaSettings\app\Models\Subregion::where('id', $addressSubregionId)->exists()) {
                            $addressSubregionId = null;
                        }

                        // Map 'address_type' to 'title' from API response
                        $addressTitle = $addressData['title'] ?? $addressData['address_type'] ?? 'Home';

                        $addressFields = [
                            'customer_id' => $customer->id,
                            'title' => $addressTitle,
                            'address' => $addressData['address'] ?? '',
                            'country_id' => $egyptCountry->id,
                            'city_id' => $addressCityId,
                            'region_id' => $addressRegionId,
                            'subregion_id' => $addressSubregionId,
                            'postal_code' => $addressData['postal_code'] ?? null,
                            'latitude' => $addressData['latitude'] ?? null,
                            'longitude' => $addressData['longitude'] ?? null,
                            'is_primary' => $addressData['is_primary'] ?? false,
                        ];

                        if (!empty($addressData['id'])) {
                            // Try to find existing address by ID
                            $existingAddress = \Modules\Customer\app\Models\CustomerAddress::where('id', $addressData['id'])->first();
                            if ($existingAddress) {
                                $existingAddress->update($addressFields);
                            } else {
                                $newAddress = new \Modules\Customer\app\Models\CustomerAddress();
                                $newAddress->id = $addressData['id'];
                                $newAddress->fill($addressFields);
                                $newAddress->save();
                            }
                        } else {
                            // Create new address without specific ID
                            \Modules\Customer\app\Models\CustomerAddress::create($addressFields);
                        }
                    }
                }
            } catch (\Exception $e) {$errors[] = "Customer {$item['name']} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting customer: " . $e->getMessage());
            }
        }

        return [
            'type' => 'customers',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'points_created' => $pointsCreated,
            'errors' => $errors,
        ];
    }

    /**
     * Set product translations
     */
    protected function setProductTranslations(Product $product, array $item): void
    {
        // Title
        if (!empty($item['title_en'])) {
            $product->setTranslation('title', 'en', $item['title_en']);
        }
        if (!empty($item['title_ar'])) {
            $product->setTranslation('title', 'ar', $item['title_ar']);
        }

        // Description
        if (!empty($item['description_en'])) {
            $product->setTranslation('description', 'en', $item['description_en']);
        }
        if (!empty($item['description_ar'])) {
            $product->setTranslation('description', 'ar', $item['description_ar']);
        }

        // Details
        if (!empty($item['details_en'])) {
            $product->setTranslation('details', 'en', $item['details_en']);
        }
        if (!empty($item['details_ar'])) {
            $product->setTranslation('details', 'ar', $item['details_ar']);
        }

        // Summary
        if (!empty($item['summary_en'])) {
            $product->setTranslation('summary', 'en', $item['summary_en']);
        }
        if (!empty($item['summary_ar'])) {
            $product->setTranslation('summary', 'ar', $item['summary_ar']);
        }

        // Instructions
        if (!empty($item['instructions_en'])) {
            $product->setTranslation('instructions', 'en', $item['instructions_en']);
        }
        if (!empty($item['instructions_ar'])) {
            $product->setTranslation('instructions', 'ar', $item['instructions_ar']);
        }

        // Features
        if (!empty($item['features_en'])) {
            $product->setTranslation('features', 'en', $item['features_en']);
        }
        if (!empty($item['features_ar'])) {
            $product->setTranslation('features', 'ar', $item['features_ar']);
        }

        // Extra description (extras)
        if (!empty($item['extras_en'])) {
            $product->setTranslation('extra_description', 'en', $item['extras_en']);
        }
        if (!empty($item['extras_ar'])) {
            $product->setTranslation('extra_description', 'ar', $item['extras_ar']);
        }

        // Material
        if (!empty($item['material_en'])) {
            $product->setTranslation('material', 'en', $item['material_en']);
        }
        if (!empty($item['material_ar'])) {
            $product->setTranslation('material', 'ar', $item['material_ar']);
        }

        // Meta title
        if (!empty($item['meta_title_en'])) {
            $product->setTranslation('meta_title', 'en', $item['meta_title_en']);
        }
        if (!empty($item['meta_title_ar'])) {
            $product->setTranslation('meta_title', 'ar', $item['meta_title_ar']);
        }

        // Meta keywords (keywords)
        if (!empty($item['keywords_en'])) {
            $product->setTranslation('meta_keywords', 'en', $item['keywords_en']);
        }
        if (!empty($item['keywords_ar'])) {
            $product->setTranslation('meta_keywords', 'ar', $item['keywords_ar']);
        }
    }

    /**
     * Create VendorProduct for a product
     */
    protected function createVendorProduct(Product $product, array $item, int $countryId): array
    {
        try {
            $vendorId = $item['brand_id']; // brand_id = vendor_id

            // Check if VendorProduct already exists
            $vendorProduct = VendorProduct::where('product_id', $product->id)
                ->where('vendor_id', $vendorId)
                ->first();

            // Prepare vendor product data - only columns that exist in vendor_products table
            $vendorProductData = [
                'sku' => $item['sku'] ?? null,
                'video_link' => $item['video_link'] ?? null,
                'max_per_order' => $item['limitation'] ?? null,
                'offer_date_view' => ($item['show_end_offer_at_section'] ?? false) == true,
                'is_active' => ($item['status'] ?? '0') == '1',
                'is_featured' => ($item['featured_product'] ?? '0') == '1',
                'status' => VendorProduct::STATUS_APPROVED,
                'sales' => 0,
                'views' => $item['views'] ?? 0,
                'country_id' => $countryId,
                'created_at' => $this->parseDate($item['created_at'] ?? null),
                'updated_at' => $this->parseDate($item['updated_at'] ?? null),
            ];

            if ($vendorProduct) {
                // Update existing
                $vendorProduct->update($vendorProductData);
                return ['created' => 0, 'vendor_product' => $vendorProduct];
            }

            // Create new VendorProduct
            $vendorProduct = new VendorProduct();
            $vendorProduct->product_id = $product->id;
            $vendorProduct->id = $item['id'];
            $vendorProduct->vendor_id = $vendorId;
            $vendorProduct->fill($vendorProductData);
            $vendorProduct->save();

            return ['created' => 1, 'vendor_product' => $vendorProduct];
        } catch (\Exception $e) {
            Log::error("Error creating VendorProduct for product {$product->id}: " . $e->getMessage());
            return ['created' => 0, 'vendor_product' => null];
        }
    }

    /**
     * Create ProductVariant (bank product variant) from product_size_colors data
     * Only creates if variant_configuration_id is present
     */
    protected function createProductVariant(Product $product, array $sizeColor): void
    {
        try {
            // Only create product variant if it has a variant_configuration_id
            $variantConfigId = $sizeColor['variants_configuration_id'] ?? $sizeColor['variant_configuration_id'] ?? null;
            
            if (!$variantConfigId) {
                // Skip - this is a simple product variant without configuration
                return;
            }
            
            // Check if product variant exists by ID
            $productVariant = ProductVariant::where('id', $sizeColor['id'])->first();

            $variantData = [
                'product_id' => $product->id,
                'variant_configuration_id' => $variantConfigId,
                'created_at' => $this->parseDate($sizeColor['created_at'] ?? null),
                'updated_at' => $this->parseDate($sizeColor['updated_at'] ?? null),
            ];

            if ($productVariant) {
                // Update existing
                $productVariant->update($variantData);
            } else {
                // Create new with same ID
                $productVariant = new ProductVariant();
                $productVariant->id = $sizeColor['id'];
                $productVariant->fill($variantData);
                $productVariant->save();
            }

        } catch (\Exception $e) {
            Log::error("Error creating ProductVariant ID {$sizeColor['id']}: " . $e->getMessage());
        }
    }

    /**
     * Create VendorProductVariant from product_size_colors data
     */
    protected function createVendorProductVariant(VendorProduct $vendorProduct, array $sizeColor, int $countryId): array
    {
        $variantCreated = 0;
        $stocksCreated = 0;

        try {
            // Check if variant exists by ID
            $variant = VendorProductVariant::where('id', $sizeColor['id'])->first();

            // Calculate price and discount
            $realPrice = $sizeColor['real_price'] ?? 0;
            $fakePrice = $sizeColor['fake_price'] ?? null;
            $discount = $sizeColor['discount'] ?? 0;
            
            // Determine if has discount
            $hasDiscount = $discount > 0 || ($fakePrice && $fakePrice > $realPrice);
            
            // Price is the selling price (after discount), price_before_discount is the original price
            $price = $realPrice;
            // price_before_discount cannot be null - use price if no discount
            $priceBeforeDiscount = $hasDiscount && $fakePrice ? $fakePrice : $price;

            $variantData = [
                'vendor_product_id' => $vendorProduct->id,
                'variant_configuration_id' => $sizeColor['variants_configuration_id'] ?? null,
                'sku' => $sizeColor['sku'] ?? null,
                'price' => $price,
                'has_discount' => $hasDiscount,
                'price_before_discount' => $priceBeforeDiscount,
                'discount_end_date' => !empty($sizeColor['end_at']) ? $this->parseDate($sizeColor['end_at']) : null,
                'country_id' => $countryId,
                'created_at' => $this->parseDate($sizeColor['created_at'] ?? null),
                'updated_at' => $this->parseDate($sizeColor['updated_at'] ?? null),
            ];

            if ($variant) {
                // Update existing
                $variant->update($variantData);
            } else {
                // Create new with same ID
                $variant = new VendorProductVariant();
                $variant->id = $sizeColor['id'];
                $variant->fill($variantData);
                $variant->save();
                $variantCreated = 1;
            }

            // Create region stocks
            if (!empty($sizeColor['region_stocks']) && is_array($sizeColor['region_stocks'])) {
                foreach ($sizeColor['region_stocks'] as $regionStock) {
                    $stocksCreated += $this->createVariantRegionStock($variant, $regionStock);
                }
            }

            // If no region_stocks but has stock field, create a default stock entry
            if (empty($sizeColor['region_stocks']) && isset($sizeColor['stock']) && $sizeColor['stock'] > 0) {
                // Get first region as default
                $defaultRegion = Region::first();
                if ($defaultRegion) {
                    $stocksCreated += $this->createVariantRegionStock($variant, [
                        'region_id' => $defaultRegion->id,
                        'stock' => $sizeColor['stock'],
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error("Error creating VendorProductVariant ID {$sizeColor['id']}: " . $e->getMessage());
        }

        return [
            'variant_created' => $variantCreated,
            'stocks_created' => $stocksCreated,
        ];
    }

    /**
     * Create VendorProductVariantStock from region_stocks data
     */
    protected function createVariantRegionStock(VendorProductVariant $variant, array $regionStock): int
    {
        try {
            $stockId = $regionStock['id'] ?? null;
            $regionId = $regionStock['region_id'] ?? null;
            $quantity = $regionStock['stock'] ?? $regionStock['quantity'] ?? 0;

            if (!$regionId) {
                return 0;
            }

            // Validate region exists
            if (!Region::where('id', $regionId)->exists()) {
                return 0;
            }

            // Check if stock exists by ID first, then by variant+region
            $stock = null;
            if ($stockId) {
                $stock = VendorProductVariantStock::where('id', $stockId)->first();
            }
            if (!$stock) {
                $stock = VendorProductVariantStock::where('vendor_product_variant_id', $variant->id)
                    ->where('region_id', $regionId)
                    ->first();
            }

            if ($stock) {
                // Update existing
                $stock->update(['quantity' => $quantity]);
                return 0;
            }

            // Create new with same ID from source
            $newStock = new VendorProductVariantStock();
            if ($stockId) {
                $newStock->id = $stockId;
            }
            $newStock->vendor_product_variant_id = $variant->id;
            $newStock->region_id = $regionId;
            $newStock->quantity = $quantity;
            $newStock->save();

            return 1;
        } catch (\Exception $e) {
            Log::error("Error creating VendorProductVariantStock for variant {$variant->id}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Download image and attach to model
     */
    protected function attachImage($model, string $imagePath, string $type): void
    {
        try {
            // Download image first
            $localPath = $this->downloadImage($imagePath);
            
            if (!$localPath) {
                return;
            }

            // For additional images, allow multiple attachments (don't check for existing)
            // For other types (main_image, icon, etc.), update existing if found
            if ($type === 'additional_image') {
                // Check if this specific image path already exists to avoid duplicates
                $existingAttachment = $model->attachments()
                    ->where('type', $type)
                    ->where('path', $localPath)
                    ->first();
                
                if (!$existingAttachment) {
                    // Create new attachment for additional image
                    Attachment::create([
                        'attachable_type' => get_class($model),
                        'attachable_id' => $model->id,
                        'path' => $localPath,
                        'type' => $type,
                    ]);
                }
            } else {
                // For main_image, icon, etc. - update existing or create new
                $existingAttachment = $model->attachments()->where('type', $type)->first();
                
                if ($existingAttachment) {
                    // Update existing attachment
                    $existingAttachment->update(['path' => $localPath]);
                } else {
                    // Create new attachment
                    Attachment::create([
                        'attachable_type' => get_class($model),
                        'attachable_id' => $model->id,
                        'path' => $localPath,
                        'type' => $type,
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error("Error attaching image {$imagePath}: " . $e->getMessage());
        }
    }

    /**
     * Download image and save locally with same path
     * Uses streaming to avoid memory exhaustion
     */
    protected function downloadImage(string $imagePath): ?string
    {
        try {
            if (empty($imagePath)) {
                return null;
            }

            // Build full URL
            $imageUrl = "{$this->sourceBaseUrl}/storage/{$imagePath}";

            // Check if file already exists locally
            if (Storage::disk('public')->exists($imagePath)) {
                Log::info("Image already exists: {$imagePath}");
                return $imagePath;
            }

            // Ensure directory exists
            $directory = dirname($imagePath);
            if ($directory && $directory !== '.') {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Get the full local path
            $localPath = Storage::disk('public')->path($imagePath);

            // Use stream to download directly to file (avoids loading into memory)
            $client = new \GuzzleHttp\Client(['verify' => false]);
            $response = $client->request('GET', $imageUrl, [
                'sink' => $localPath,
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);
            
            if ($response->getStatusCode() !== 200) {
                Log::warning("Failed to download image: {$imageUrl} - Status: {$response->getStatusCode()}");
                // Clean up partial file
                if (file_exists($localPath)) {
                    @unlink($localPath);
                }
                return null;
            }
            
            Log::info("Image downloaded: {$imagePath}");
            
            // Force garbage collection to free memory
            gc_collect_cycles();
            
            return $imagePath;

        } catch (\Exception $e) {
            Log::error("Error downloading image {$imagePath}: " . $e->getMessage());
            // Clean up partial file
            $localPath = Storage::disk('public')->path($imagePath);
            if (file_exists($localPath)) {
                @unlink($localPath);
            }
            return null;
        }
    }

    /**
     * Parse date string from API format to Carbon instance
     * Handles format like "21 Oct, 2025, 04:19 PM"
     */
    protected function parseDate(?string $dateString): ?\Carbon\Carbon
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Try parsing the API format: "21 Oct, 2025, 04:19 PM"
            return \Carbon\Carbon::createFromFormat('d M, Y, h:i A', $dateString);
        } catch (\Exception $e) {
            try {
                // Fallback: try standard parsing
                return \Carbon\Carbon::parse($dateString);
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    /**
     * Inject bundle categories into database
     */
    protected function injectBundleCategories(array $data): array
    {
        $items = $data['bundle_categories']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $errors = [];

        foreach ($items as $item) {
            try {
                $bundleCategory = BundleCategory::where('id', $item['id'])->first();

                if ($bundleCategory) {
                    $bundleCategory->update([
                        'slug' => $item['slug_en'] ?? $item['slug'] ?? null,
                        'active' => ($item['status'] ?? '1') == '1',
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    $bundleCategory = new BundleCategory();
                    $bundleCategory->id = $item['id'];
                    $bundleCategory->slug = $item['slug_en'] ?? $item['slug'] ?? null;
                    $bundleCategory->active = ($item['status'] ?? '1') == '1';
                    $bundleCategory->created_at = $this->parseDate($item['created_at'] ?? null);
                    $bundleCategory->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $bundleCategory->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['title_en'])) {
                    $bundleCategory->setTranslation('name', 'en', $item['title_en']);
                }
                if (!empty($item['title_ar'])) {
                    $bundleCategory->setTranslation('name', 'ar', $item['title_ar']);
                }
                if (!empty($item['description_en'])) {
                    $bundleCategory->setTranslation('description', 'en', $item['description_en']);
                }
                if (!empty($item['description_ar'])) {
                    $bundleCategory->setTranslation('description', 'ar', $item['description_ar']);
                }
                $bundleCategory->save();

                // Download and attach image
                if (!empty($item['image'])) {
                    $this->attachImage($bundleCategory, $item['image'], 'image');
                }
            } catch (\Exception $e) {
                $titleEn = $item['title_en'] ?? $item['id'];
                $errors[] = "BundleCategory {$titleEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting bundle category: " . $e->getMessage());
            }
        }

        return [
            'type' => 'bundle_categories',
            'injected' => $injected,
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    /**
     * Inject bundles into database
     */
    protected function injectBundles(array $data): array
    {
        $items = $data['bundles']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $bundleProductsCreated = 0;
        $errors = [];

        // Get default vendor (first one or ID 1)
        $defaultVendorId = Vendor::first()?->id ?? 1;

        foreach ($items as $item) {
            try {
                $bundle = Bundle::where('id', $item['id'])->first();

                if ($bundle) {
                    $bundle->update([
                        'slug' => $item['slug_en'] ?? $item['slug'] ?? null,
                        'sku' => $item['sku'] ?? null,
                        'bundle_category_id' => $item['bundle_category_id'] ?? null,
                        'vendor_id' => $item['vendor_id'] ?? $defaultVendorId,
                        'is_active' => ($item['status'] ?? '1') == '1',
                        'admin_approval' => 1,
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    // Delete existing bundle products to re-create
                    $bundle->bundleProducts()->forceDelete();
                    $updated++;
                } else {
                    $bundle = new Bundle();
                    $bundle->id = $item['id'];
                    $bundle->slug = $item['slug_en'] ?? $item['slug'] ?? null;
                    $bundle->sku = $item['sku'] ?? null;
                    $bundle->bundle_category_id = $item['bundle_category_id'] ?? null;
                    $bundle->vendor_id = $item['vendor_id'] ?? $defaultVendorId;
                    $bundle->is_active = ($item['status'] ?? '1') == '1';
                    $bundle->admin_approval = 1;
                    $bundle->created_at = $this->parseDate($item['created_at'] ?? null);
                    $bundle->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $bundle->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['name_en'])) {
                    $bundle->setTranslation('name', 'en', $item['name_en']);
                }
                if (!empty($item['name_ar'])) {
                    $bundle->setTranslation('name', 'ar', $item['name_ar']);
                }
                if (!empty($item['description_en'])) {
                    $bundle->setTranslation('description', 'en', $item['description_en']);
                }
                if (!empty($item['description_ar'])) {
                    $bundle->setTranslation('description', 'ar', $item['description_ar']);
                }
                if (!empty($item['meta_description_en'])) {
                    $bundle->setTranslation('meta_description', 'en', $item['meta_description_en']);
                }
                if (!empty($item['meta_description_ar'])) {
                    $bundle->setTranslation('meta_description', 'ar', $item['meta_description_ar']);
                }
                $bundle->save();

                // Download and attach image
                if (!empty($item['image'])) {
                    $this->attachImage($bundle, $item['image'], 'main_image');
                }

                // Create bundle products
                if (!empty($item['bundle_products']) && is_array($item['bundle_products'])) {
                    foreach ($item['bundle_products'] as $bp) {
                        $variantId = $bp['product_size_color_id'] ?? null;
                        // Skip if variant doesn't exist locally
                        if (!$variantId || !VendorProductVariant::where('id', $variantId)->exists()) {
                            continue;
                        }
                        BundleProduct::create([
                            'bundle_id' => $bundle->id,
                            'vendor_product_variant_id' => $variantId,
                            'min_quantity' => $bp['quantity'] ?? $bp['minimum'] ?? 1,
                            'limitation_quantity' => $bp['limitation'] ?? null,
                            'price' => $bp['price'] ?? 0,
                        ]);
                        $bundleProductsCreated++;
                    }
                }
            } catch (\Exception $e) {
                $nameEn = $item['name_en'] ?? $item['id'];
                $errors[] = "Bundle {$nameEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting bundle: " . $e->getMessage());
            }
        }

        return [
            'type' => 'bundles',
            'injected' => $injected,
            'updated' => $updated,
            'bundle_products_created' => $bundleProductsCreated,
            'errors' => $errors,
        ];
    }

    /**
     * Inject admins into database
     */
    protected function injectAdmins(array $data): array
    {
        $items = $data['admins']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
        
        // Get admin user type ID (user_type_id = 2)
        $adminUserTypeId = 2;
        
        // Get admin role
        $adminRole = Role::where('type', 'admin')
            ->first();
        
        if (!$adminRole) {
            return [
                'type' => 'admins',
                'injected' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => ['Admin role not found in database. Please create an admin role first.'],
            ];
        }
        
        Log::info("=== Starting Admin Injection ===", [
            'total_items' => count($items),
            'admin_user_type_id' => $adminUserTypeId,
            'admin_role_id' => $adminRole->id,
            'admin_role_name' => $adminRole->name,
        ]);

        foreach ($items as $item) {
            try {
                $adminId = $item['id'] ?? null;
                $email = $item['email'] ?? null;
                $name = $item['name'] ?? null;
                
                if (!$email || !$name) {
                    $skipped++;
                    Log::warning("Admin skipped: Missing email or name", ['item' => $item]);
                    continue;
                }
                
                Log::info("Processing Admin", [
                    'id' => $adminId,
                    'name' => $name,
                    'email' => $email,
                ]);
                
                // Check if user exists by email (more reliable than ID)                
                // Create new admin user
                $user = new User();
                if ($adminId) {
                    $user->id = $adminId;
                }
                $user->email = $email;
                $user->phone = $item['phone'] ?? null;
                $user->password = $item['password']; // Default password
                $user->user_type_id = $adminUserTypeId;
                $user->created_at = $this->parseDate($item['created_at'] ?? null);
                $user->updated_at = $this->parseDate($item['updated_at'] ?? null);
                $user->save();
                
                // Set name in translations (not direct field)
                $user->setTranslation('name', 'en', $name);
                $user->setTranslation('name', 'ar', $name); // Use same name for both languages
                $user->save();
                
                // Attach admin role
                $user->roles()->attach($adminRole->id);
                
                $injected++;
                Log::info("Admin CREATED", [
                    'email' => $email,
                    'name' => $name,
                    'default_password' => 'password123'
                ]);
                
                // Download and attach profile image if exists
                if (!empty($item['image'])) {
                    $this->attachImage($user, $item['image'], 'profile_image');
                }
                
            } catch (\Exception $e) {
                $name = $item['name'] ?? $item['email'] ?? $item['id'] ?? 'unknown';
                $error = "Admin {$name}: " . $e->getMessage();
                $errors[] = $error;
                Log::error("Admin injection error", [
                    'name' => $name,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
        
        Log::info("=== Admin Injection Complete ===", [
            'total_items' => count($items),
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors_count' => count($errors),
        ]);

        return [
            'type' => 'admins',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
            'note' => 'New admins created with default password: password123',
        ];
    }
}
