<?php

namespace Modules\CatalogManagement\app\Actions;

use Modules\CatalogManagement\app\Services\VariantConfigurationKeyService;
use App\Services\LanguageService;
use App\Traits\Res;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\CatalogManagement\app\Interfaces\VariantConfigurationKeyRepositoryInterface;

class VariantConfigurationKeyAction {

    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected VariantConfigurationKeyService $variantKeyService,
        protected VariantConfigurationKeyRepositoryInterface $variantKeyRepositoryInterface
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
                'search' => $data['search'] ?? null,
                'created_date_from' => $data['created_date_from'] ?? null,
                'created_date_to' => $data['created_date_to'] ?? null,
            ];
            
            // Get total and filtered counts
            $totalRecords = $this->variantKeyRepositoryInterface->getVariantConfigurationKeysQuery([])->count();
            $filteredRecords = $this->variantKeyRepositoryInterface->getVariantConfigurationKeysQuery($filters)->count();
            
            // Get languages
            $languages = $this->languageService->getAll();
            
            // Get all variant keys with filters and eager load relationships
            $variantKeysQuery = $this->variantKeyRepositoryInterface->getVariantConfigurationKeysQuery($filters);
            $variantKeysCollection = $variantKeysQuery->with(['translations', 'parent.translations'])->get();
            
            // Apply sorting using Eloquent collections
            if (isset($data['orderColumnIndex']) && isset($data['orderDirection'])) {
                $columnIndex = $data['orderColumnIndex'];
                $direction = $data['orderDirection'];
                
                switch ($columnIndex) {
                    case 0: // ID column
                        $variantKeysCollection = $direction === 'asc' 
                            ? $variantKeysCollection->sortBy('id') 
                            : $variantKeysCollection->sortByDesc('id');
                        break;
                        
                    case 1: // First language name (usually EN)
                        $firstLang = $languages->first();
                        if ($firstLang) {
                            $variantKeysCollection = $direction === 'asc'
                                ? $variantKeysCollection->sortBy(function($variantKey) use ($firstLang) {
                                    $translation = $variantKey->translations
                                        ->where('lang_id', $firstLang->id)
                                        ->where('lang_key', 'name')
                                        ->first();
                                    return $translation ? $translation->lang_value : '';
                                })
                                : $variantKeysCollection->sortByDesc(function($variantKey) use ($firstLang) {
                                    $translation = $variantKey->translations
                                        ->where('lang_id', $firstLang->id)
                                        ->where('lang_key', 'name')
                                        ->first();
                                    return $translation ? $translation->lang_value : '';
                                });
                        }
                        break;
                        
                    case 2: // Second language name (usually AR)
                        $secondLang = $languages->skip(1)->first();
                        if ($secondLang) {
                            $variantKeysCollection = $direction === 'asc'
                                ? $variantKeysCollection->sortBy(function($variantKey) use ($secondLang) {
                                    $translation = $variantKey->translations
                                        ->where('lang_id', $secondLang->id)
                                        ->where('lang_key', 'name')
                                        ->first();
                                    return $translation ? $translation->lang_value : '';
                                })
                                : $variantKeysCollection->sortByDesc(function($variantKey) use ($secondLang) {
                                    $translation = $variantKey->translations
                                        ->where('lang_id', $secondLang->id)
                                        ->where('lang_key', 'name')
                                        ->first();
                                    return $translation ? $translation->lang_value : '';
                                });
                        }
                        break;
                        
                    case 3: // Created At column (now case 3 since we removed parent filter)
                        $variantKeysCollection = $direction === 'asc'
                            ? $variantKeysCollection->sortBy('created_at')
                            : $variantKeysCollection->sortByDesc('created_at');
                        break;
                        
                    default:
                        $variantKeysCollection = $variantKeysCollection->sortByDesc('id');
                        break;
                }
            } else {
                // Default sorting
                $variantKeysCollection = $variantKeysCollection->sortByDesc('id');
            }
            
            // Manual pagination
            $offset = ($page - 1) * $perPage;
            $variantKeys = $variantKeysCollection->slice($offset, $perPage)->values();
            
            // Create paginator instance
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $variantKeys,
                $variantKeysCollection->count(),
                $perPage,
                $page,
                ['path' => request()->url()]
            );
            
            // Build data array for DataTables
            $data = [];
            foreach ($variantKeys as $index => $variantKey) {
                $rowData = [
                    'index' => $index + 1,
                    'id' => $variantKey->id,
                    'translations' => [],
                    'parent' => null,
                    'created_at' => $variantKey->created_at->format('Y-m-d H:i'),
                ];
                
                // Add translations for each language
                foreach ($languages as $language) {
                    $translation = $variantKey->translations
                        ->where('lang_id', $language->id)
                        ->where('lang_key', 'name')
                        ->first();
                    $rowData['translations'][$language->code] = [
                        'name' => $translation ? $translation->lang_value : '-',
                        'rtl' => $language->rtl
                    ];
                }
                
                // Add parent information if exists
                if ($variantKey->parent) {
                    $parentTranslation = $variantKey->parent->translations
                        ->where('lang_key', 'name')
                        ->first();
                    $rowData['parent'] = $parentTranslation ? $parentTranslation->lang_value : '-';
                }
                
                // Add first translation name for delete modal
                $firstTranslation = $variantKey->translations
                    ->where('lang_key', 'name')
                    ->first();
                $rowData['first_name'] = $firstTranslation ? $firstTranslation->lang_value : '';
                
                $data[] = $rowData;
            }
            
            return [
                'data' => $data,
                'totalRecords' => $totalRecords,
                'filteredRecords' => $filteredRecords,
                'dataPaginated' => $paginator
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in VariantConfigurationKeyAction getDataTable: ' . $e->getMessage());
            return [
                'data' => [],
                'totalRecords' => 0,
                'filteredRecords' => 0,
                'dataPaginated' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ];
        }
    }
        
}
