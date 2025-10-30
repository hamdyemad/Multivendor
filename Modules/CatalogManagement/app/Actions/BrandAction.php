<?php

namespace Modules\CatalogManagement\app\Actions;

use Modules\CatalogManagement\app\Services\BrandService;
use App\Services\LanguageService;
use App\Traits\Res;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\CatalogManagement\app\Interfaces\BrandRepositoryInterface;

class BrandAction {

    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected BrandService $brandService,
        protected BrandRepositoryInterface $brandRepositoryInterface
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
                'search' => $data['search'],
                'active' => $data['active'],
                'created_date_from' => $data['created_date_from'],
                'created_date_to' => $data['created_date_to'],
            ];
            
            // Get total and filtered counts
            $totalRecords = $this->brandRepositoryInterface->getBrandsQuery([])->count();
            $filteredRecords = $this->brandRepositoryInterface->getBrandsQuery($filters)->count();
            
            // Get brands with pagination
            $brandsQuery = $this->brandRepositoryInterface->getBrandsQuery($filters);
            $brands = $brandsQuery->paginate($perPage, ['*'], 'page', $page);
            
            // Get languages
            $languages = $this->languageService->getAll();
            
            // Return raw data - rendering will be handled by DataTables in the view
            $data = [];
            foreach ($brands as $brand) {
                $rowData = [
                    'id' => $brand->id,
                    'logo_path' => $brand->logo ? $brand->logo->path : null,
                    'translations' => [],
                    'active' => $brand->active,
                    'created_at' => $brand->created_at->format('Y-m-d H:i'),
                ];
                
                // Add translations for each language
                foreach ($languages as $language) {
                    $translation = $brand->translations->where('lang_id', $language->id)
                        ->where('lang_key', 'name')
                        ->first();
                    $rowData['translations'][$language->code] = [
                        'name' => $translation ? $translation->lang_value : '-',
                        'rtl' => $language->rtl
                    ];
                }
                
                // Add first translation name for delete modal
                $firstTranslation = $brand->translations->where('lang_key', 'name')->first();
                $rowData['first_name'] = $firstTranslation ? $firstTranslation->lang_value : '';
                
                $data[] = $rowData;
            }
            
            return [
                'data' => $data,
                'totalRecords' => $totalRecords,
                'filteredRecords' => $filteredRecords,
                'dataPaginated' => $brands
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in BrandAction getDataTable: ' . $e->getMessage());
            return [
                'data' => [],
                'totalRecords' => 0,
                'filteredRecords' => 0,
                'dataPaginated' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ];
        }
    }
        
}
