<?php

namespace Modules\CatalogManagement\app\Actions;

use Modules\CatalogManagement\app\Services\TaxService;
use App\Services\LanguageService;
use App\Traits\Res;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\CatalogManagement\app\Interfaces\TaxRepositoryInterface;

class TaxAction {

    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected TaxService $taxService,
        protected TaxRepositoryInterface $taxRepositoryInterface
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
            
            // Get sorting parameters
            $orderColumnIndex = $data['orderColumnIndex'] ?? 0;
            $orderDirection = $data['orderDirection'] ?? 'desc';
            
            // Get filter parameters
            $filters = [
                'search' => $data['search'],
                'active' => $data['active'],
                'created_date_from' => $data['created_date_from'],
                'created_date_to' => $data['created_date_to'],
            ];
            
            // Get languages
            $languages = $this->languageService->getAll();
            
            // Get total and filtered counts
            $totalRecords = $this->taxRepositoryInterface->getTaxesQuery([])->count();
            $filteredRecords = $this->taxRepositoryInterface->getTaxesQuery($filters)->count();
            
            // Determine sort column
            $orderBy = null;
            if ($orderColumnIndex == 0) {
                $orderBy = 'id';
            } elseif ($orderColumnIndex >= 1 && $orderColumnIndex <= count($languages)) {
                // Sorting by translated name column
                $languageIndex = $orderColumnIndex - 1;
                $selectedLanguage = $languages->values()->get($languageIndex);
                if ($selectedLanguage) {
                    $orderBy = [
                        'lang_id' => $selectedLanguage->id,
                        'key' => 'name'
                    ];
                }
            } elseif ($orderColumnIndex == count($languages) + 1) {
                $orderBy = 'tax_rate';
            } elseif ($orderColumnIndex == count($languages) + 2) {
                $orderBy = 'active';
            } elseif ($orderColumnIndex == count($languages) + 3) {
                $orderBy = 'created_at';
            }
            
            // Get taxes with pagination and sorting
            $taxesQuery = $this->taxRepositoryInterface->getTaxesQuery($filters, $orderBy, $orderDirection);
            $taxes = $taxesQuery->paginate($perPage, ['*'], 'page', $page);
            
            // Return raw data - rendering will be handled by DataTables in the view
            $data = [];
            foreach ($taxes as $tax) {
                $rowData = [
                    'id' => $tax->id,
                    'translations' => [],
                    'tax_rate' => $tax->tax_rate,
                    'active' => $tax->active,
                    'created_at' => $tax->created_at->format('Y-m-d H:i'),
                ];
                
                // Add translations for each language
                foreach ($languages as $language) {
                    $translation = $tax->translations->where('lang_id', $language->id)
                        ->where('lang_key', 'name')
                        ->first();
                    $rowData['translations'][$language->code] = [
                        'name' => $translation ? $translation->lang_value : '-',
                        'rtl' => $language->rtl
                    ];
                }
                
                // Add first translation name for delete modal
                $firstTranslation = $tax->translations->where('lang_key', 'name')->first();
                $rowData['first_name'] = $firstTranslation ? $firstTranslation->lang_value : '';
                
                $data[] = $rowData;
            }
            
            return [
                'data' => $data,
                'totalRecords' => $totalRecords,
                'filteredRecords' => $filteredRecords,
                'dataPaginated' => $taxes
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in TaxAction getDataTable: ' . $e->getMessage());
            return [
                'data' => [],
                'totalRecords' => 0,
                'filteredRecords' => 0,
                'dataPaginated' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ];
        }
    }
        
}
