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

            // Get total records count
            $totalRecords = Product::count();

            // Build query with filters
            $query = Product::with(['brand', 'category', 'variants', 'translations'])->filter($filters);
            $filteredRecords = $query->count();

            // Apply pagination
            $products = $query->latest()->paginate($perPage, ['*'], 'page', $page);
            // Get languages
            $languages = $this->languageService->getAll();

            // Return raw data - rendering will be handled by DataTables in the view
            $data = [];
            $index = 1;
            foreach ($products as $product) {
                $rowData = [
                    'id' => $product->id,
                    'index' => $index++,
                    'translations' => [],
                    'brand' => $product->brand ? [
                        'id' => $product->brand->id,
                        'name' => $product->brand->getTranslation('name', app()->getLocale()) ??
                                 $product->brand->getTranslation('name', 'en') ??
                                 $product->brand->getTranslation('name', 'ar') ?? '-'
                    ] : null,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->getTranslation('name', app()->getLocale()) ??
                                 $product->category->getTranslation('name', 'en') ??
                                 $product->category->getTranslation('name', 'ar') ?? '-'
                    ] : null,
                    'active' => $product->is_active,
                    'created_at' => $product->created_at,
                ];

                // Add translations for each language
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
