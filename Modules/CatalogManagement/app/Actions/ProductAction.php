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
                'is_active' => $data['active'] ?? null,
                'created_date_from' => $data['created_date_from'] ?? '',
                'created_date_to' => $data['created_date_to'] ?? '',
            ];

            // Get current user and user type
            $currentUser = Auth::user();
            $userType = $currentUser ? $currentUser->user_type_id : null;

            // Get total records count - filter by vendor if user is vendor
            $totalQuery = Product::query();
            if ($currentUser && in_array($userType, UserType::vendorIds())) {
                // Vendor users see only their own products
                $totalQuery->where('created_by_user_id', $currentUser->id);
            }
            // Admin users see all products (no filter applied)
            $totalRecords = $totalQuery->count();

            // Build query with filters
            $query = Product::with(['brand', 'category', 'variants', 'translations', 'vendor'])->filter($filters);

            // Filter by vendor if user is vendor
            if ($currentUser && in_array($userType, UserType::vendorIds())) {
                // Vendor users see only their own products
                $query->where('created_by_user_id', $currentUser->id);
            }
            // Admin users see all products (no filter applied)

            $filteredRecords = $query->count();

            // Apply pagination
            $products = $query->latest()->paginate($perPage, ['*'], 'page', $page);
            // Get languages
            $languages = $this->languageService->getAll();

            // Return raw data - rendering will be handled by DataTables in the view
            $data = [];
            $index = 1;
            foreach ($products as $product) {
                // Get product names in EN and AR
                $nameEn = $product->getTranslation('title', 'en') ?? '-';
                $nameAr = $product->getTranslation('title', 'ar') ?? '-';

                $rowData = [
                    'id' => $product->id,
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
                    'active' => $product->is_active,
                    'created_at' => $product->created_at,
                ];

                // Add vendor information for admin users
                if ($currentUser && in_array($userType, UserType::adminIds())) {
                    if ($product->vendor) {
                        $vendorName = truncateString($product->vendor->name);

                        $rowData['vendor'] = [
                            'id' => $product->vendor->id,
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

}
