<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Services\SubCategoryService;
use App\Services\LanguageService;
use App\Traits\Res;
use Illuminate\Support\Facades\Log;
use Modules\CategoryManagment\app\Interfaces\SubCategoryRepositoryInterface;

class SubCategoryAction {

    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected SubCategoryService $subCategoryService,
        protected SubCategoryRepositoryInterface $subCategoryRepositoryInterface
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
            $sortType = $data['sort_type'] ?? 'id';
            $sortBy = $data['sort_by'] ?? 'desc';
            
            // Get filter parameters
            $filters = [
                'search' => $data['search'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'active' => $data['active'] ?? null,
                'created_date_from' => $data['created_date_from'] ?? null,
                'created_date_to' => $data['created_date_to'] ?? null,
            ];
            
            // Get languages
            $languages = $this->languageService->getAll();
            
            // Get total and filtered counts
            $totalRecords = $this->subCategoryRepositoryInterface->getSubCategoriesQuery([])->count();
            $filteredRecords = $this->subCategoryRepositoryInterface->getSubCategoriesQuery($filters)->count();
            
            // Determine sort column based on sort_type
            $orderBy = null;
            if ($sortType == 'id') {
                $orderBy = 'id';
            } elseif (str_starts_with($sortType, 'name_')) {
                // Sorting by translated name column (e.g., name_en, name_ar)
                $languageCode = str_replace('name_', '', $sortType);
                $selectedLanguage = $languages->firstWhere('code', $languageCode);
                if ($selectedLanguage) {
                    $orderBy = [
                        'lang_id' => $selectedLanguage->id,
                        'key' => 'name'
                    ];
                }
            } elseif ($sortType == 'category') {
                $orderBy = 'category_id';
            } elseif ($sortType == 'active') {
                $orderBy = 'active';
            } elseif ($sortType == 'created_at') {
                $orderBy = 'created_at';
            }
            
            $filters['orderBy'] = $orderBy;
            $filters['sortBy'] = $sortBy;            
            // Get subcategories with pagination and sorting
            $subCategoriesQuery = $this->subCategoryRepositoryInterface->getSubCategoriesQuery($filters);
            $subCategories = $subCategoriesQuery->paginate($perPage, ['*'], 'page', $page);
            
            // Return raw data - rendering will be handled by DataTables in the view
            $data = [];
            foreach ($subCategories as $index => $subCategory) {
                $rowData = [
                    'index' => $index + 1,
                    'id' => $subCategory->id,
                    'translations' => [],
                    'category' => null,
                    'active' => $subCategory->active,
                    'created_at' => $subCategory->created_at->format('Y-m-d H:i'),
                ];
                
                // Add translations for each language
                foreach ($languages as $language) {
                    $translation = $subCategory->translations->where('lang_id', $language->id)
                        ->where('lang_key', 'name')
                        ->first();
                    $rowData['translations'][$language->code] = [
                        'name' => $translation ? $translation->lang_value : '-',
                        'rtl' => $language->rtl
                    ];
                }
                
                // Add category info
                if ($subCategory->category) {
                    $rowData['category'] = [
                        'id' => $subCategory->category->id,
                        'name' => $subCategory->category->getTranslation('name', app()->getLocale())
                    ];
                }
                
                // Add first translation name for delete modal
                $firstTranslation = $subCategory->translations->where('lang_key', 'name')->first();
                $rowData['first_name'] = $firstTranslation ? $firstTranslation->lang_value : '';
                
                $data[] = $rowData;
            }
            
            return [
                'data' => $data,
                'totalRecords' => $totalRecords,
                'filteredRecords' => $filteredRecords,
                'dataPaginated' => $subCategories
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in SubCategoryAction getDataTable: ' . $e->getMessage());
            return [
                'data' => [],
                'totalRecords' => 0,
                'filteredRecords' => 0,
                'dataPaginated' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ];
        }
    }
        
}
