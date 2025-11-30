<?php

namespace Modules\CatalogManagement\app\Actions;

use Modules\CatalogManagement\app\Services\ProductService;
use App\Services\LanguageService;
use App\Traits\Res;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\CatalogManagement\app\Interfaces\ProductInterface;
use Modules\CatalogManagement\app\Models\Product;
use App\Models\UserType;
use Illuminate\Support\Facades\Auth;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\Vendor\app\Models\Vendor;

class ProductAction {

    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected ProductService $productService,
        protected ProductInterface $productInterface
        ) {}

    /**
     * Datatable endpoint for server-side processing
     */
    public function getDataTable($data)
    {
        try {
            // Get pagination parameters
            $perPage = $data['per_page'] ?? $data['length'] ?? 10;
            $page = $data['page'] ?? 1;

            // Get filter parameters
            $filters = [
                'search' => $data['search'] ?? '',
                'vendor_id' => $data['vendor_id'] ?? null,
                'brand_id' => $data['brand_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'product_type' => $data['product_type'] ?? null,
                'is_active' => $data['active'] ?? null,
                'status' => $data['status'] ?? null,
                'created_date_from' => $data['created_date_from'] ?? '',
                'created_date_to' => $data['created_date_to'] ?? '',
                'type' => $data['type'] ?? null,
            ];

            // Get current user and user type
            $currentUser = Auth::user();
            $userType = $currentUser ? $currentUser->user_type_id : null;
            $vendorId = null;

            // Only apply vendor filter for vendor users
            if ($currentUser && in_array($userType, UserType::vendorIds())) {
                $vendorId = $currentUser->vendor->id ?? null;
            }

            $query = VendorProduct::with([
                'product.brand',
                'product.category',
                'product.translations',
                'vendor'
            ]);

            // Apply vendor filter only for vendor users
            if($vendorId) {
                $query->where('vendor_id', $vendorId);
            }

            // Apply other filters
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->whereHas('product', function($q) use ($search) {
                    $q->whereHas('translations', function($query) use ($search) {
                        $query->where('lang_value', 'like', "%{$search}%");
                    })
                    ->orWhereHas('brand', function($query) use ($search) {
                        $query->whereHas('translations', function($subQuery) use ($search) {
                            $subQuery->where('lang_value', 'like', "%{$search}%");
                        });
                    })
                    ->orWhereHas('category', function($query) use ($search) {
                        $query->whereHas('translations', function($subQuery) use ($search) {
                            $subQuery->where('lang_value', 'like', "%{$search}%");
                        });
                    });
                });
            }

            if (!empty($filters['brand_id'])) {
                $query->whereHas('product', function($q) use ($filters) {
                    $q->where('brand_id', $filters['brand_id']);
                });
            }

            if (!empty($filters['category_id'])) {
                $query->whereHas('product', function($q) use ($filters) {
                    $q->where('category_id', $filters['category_id']);
                });
            }

            if (!empty($filters['vendor_id'])) {
                $query->where('vendor_id', $filters['vendor_id']);
            }

            if (!empty($filters['product_type'])) {
                $query->whereHas('product', function($q) use ($filters) {
                    $q->where('type', $filters['product_type']);
                });
            }

            if (isset($filters['is_active']) && $filters['is_active'] !== '') {
                $query->where('is_active', (bool)$filters['is_active']);
            }

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['created_date_from'])) {
                $query->whereDate('created_at', '>=', $filters['created_date_from']);
            }

            if (!empty($filters['created_date_to'])) {
                $query->whereDate('created_at', '<=', $filters['created_date_to']);
            }

            // Get total records based on vendor filter
            if($vendorId) {
                $totalRecords = VendorProduct::where('vendor_id', $vendorId)->count();
            } else {
                $totalRecords = VendorProduct::count();
            }

            $filteredRecords = $query->count();

            // Apply pagination
            $products = $query->latest()->paginate($perPage, ['*'], 'page', $page);
            // Get languages
            $languages = $this->languageService->getAll();

            // Return raw data - rendering will be handled by DataTables in the view
            $data = [];
            $index = 1;
            foreach ($products as $item) {
                // $item is VendorProduct, so we need to access the product relationship
                $product = $item->product;

                // Get product names in EN and AR
                $nameEn = $product->getTranslation('title', 'en') ?? '-';
                $nameAr = $product->getTranslation('title', 'ar') ?? '-';

                $rowData = [
                    'id' => $product->id,
                    'vendor_product_id' => $item->id,
                    'index' => $index++,
                    'product_information' => [
                        'name_en' => truncateString($nameEn),
                        'name_ar' => truncateString($nameAr),
                    ],
                    'translations' => [],
                    'brand' => $product->brand ? [
                        'id' => $product->brand->id,
                        'name' => truncateString($product->brand->name),
                    ] : null,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => truncateString($product->category->name),
                    ] : null,
                    // For bank products, use Product.is_active; for regular products, use VendorProduct.is_active
                    'active' => $item->is_active,
                    'status' => $item->status,
                    'product_type' => $product->type,
                    'created_at' => $item->created_at,
                ];

                // Add vendor information for admin users
                if ($currentUser && $userType && in_array($userType, UserType::adminIds())) {
                    if ($item->vendor) {
                        $vendorName = truncateString($item->vendor->name);

                        $rowData['vendor'] = [
                            'id' => $item->vendor->id,
                            'name' => $vendorName
                        ];
                    } else {
                        $rowData['vendor'] = null;
                    }
                }

                // Add translations for each language (keeping for backward compatibility)
                foreach ($languages as $language) {
                    $translation = $product->translations->where('lang_id', $language->id)
                        ->where('lang_key', 'title')
                        ->first();
                    $rowData['translations'][$language->code] = [
                        'name' => $translation ? $translation->lang_value : '-',
                        'rtl' => $language->rtl
                    ];
                }

                $data[] = $rowData;
            }

            return [
                'data' => $data,
                'totalRecords' => $totalRecords,
                'filteredRecords' => $filteredRecords,
                'dataPaginated' => $products
            ];

        } catch (\Exception $e) {
            Log::error('Error in ProductAction getDataTable: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'data' => [],
                'totalRecords' => 0,
                'filteredRecords' => 0,
                'dataPaginated' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ];
        }
    }

    /**
     * Datatable endpoint for bank products (queries Product directly, not VendorProduct)
     */
    public function getBankDataTable($data)
    {
        try {
            // Get pagination parameters
            $perPage = $data['per_page'] ?? $data['length'] ?? 10;
            $page = $data['page'] ?? 1;

            // Get filter parameters
            $filters = [
                'search' => $data['search'] ?? '',
                'brand_id' => $data['brand_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'is_active' => $data['active'] ?? null,
                'created_date_from' => $data['created_date_from'] ?? '',
                'created_date_to' => $data['created_date_to'] ?? '',
            ];

            // Get languages for translations
            $languages = $this->languageService->getAll();

            // Build query for bank products (directly from Product table)
            $query = Product::with(['brand', 'category', 'translations'])
                ->where('type', Product::TYPE_BANK);

            // Apply filters
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    $q->whereHas('translations', function($query) use ($search) {
                        $query->where('lang_value', 'like', "%{$search}%");
                    })
                    ->orWhereHas('brand', function($query) use ($search) {
                        $query->whereHas('translations', function($subQuery) use ($search) {
                            $subQuery->where('lang_value', 'like', "%{$search}%");
                        });
                    })
                    ->orWhereHas('category', function($query) use ($search) {
                        $query->whereHas('translations', function($subQuery) use ($search) {
                            $subQuery->where('lang_value', 'like', "%{$search}%");
                        });
                    });
                });
            }

            if (!empty($filters['brand_id'])) {
                $query->where('brand_id', $filters['brand_id']);
            }

            if (!empty($filters['category_id'])) {
                $query->where('category_id', $filters['category_id']);
            }

            if (isset($filters['is_active']) && $filters['is_active'] !== '') {
                $query->where('is_active', (bool)$filters['is_active']);
            }

            if (!empty($filters['created_date_from'])) {
                $query->whereDate('created_at', '>=', $filters['created_date_from']);
            }

            if (!empty($filters['created_date_to'])) {
                $query->whereDate('created_at', '<=', $filters['created_date_to']);
            }

            // Get total count before pagination
            $totalRecords = Product::where('type', Product::TYPE_BANK)->count();
            $filteredRecords = $query->count();

            // Apply pagination
            $products = $query->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            // Format data for DataTables
            $formattedData = [];
            $index = ($page - 1) * $perPage + 1;

            foreach ($products as $product) {
                // Get product names in EN and AR
                $nameEn = $product->getTranslation('title', 'en') ?? '-';
                $nameAr = $product->getTranslation('title', 'ar') ?? '-';

                $formattedData[] = [
                    'id' => $product->id,
                    'index' => $index++,
                    'product_information' => [
                        'name_en' => truncateString($nameEn),
                        'name_ar' => truncateString($nameAr),
                    ],
                    'brand' => $product->brand ? [
                        'id' => $product->brand->id,
                        'name' => truncateString($product->brand->name),
                    ] : null,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => truncateString($product->category->name),
                    ] : null,
                    'active' => $product->is_active, // Product.is_active for bank products
                    'created_at' => $product->created_at,
                ];
            }

            return [
                'data' => $formattedData,
                'totalRecords' => $totalRecords,
                'filteredRecords' => $filteredRecords,
                'dataPaginated' => $products
            ];

        } catch (\Exception $e) {
            Log::error('Error in ProductAction getBankDataTable: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'data' => [],
                'totalRecords' => 0,
                'filteredRecords' => 0,
                'dataPaginated' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ];
        }
    }

}
