<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Services\CategoryService;
use App\Services\LanguageService;
use App\Traits\Res;
use Illuminate\Support\Facades\Log;
use Modules\CategoryManagment\app\Interfaces\CategoryRepositoryInterface;

class CategoryAction {

    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected CategoryService $categoryService,
        protected CategoryRepositoryInterface $categoryRepositoryInterface
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
            
            Log::info('CategoryAction - Sorting Parameters', [
                'orderColumnIndex' => $orderColumnIndex,
                'orderDirection' => $orderDirection,
                'all_data' => $data
            ]);
            
            // Get filter parameters
            $filters = [
                'search' => $data['search'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'active' => $data['active'] ?? null,
                'created_date_from' => $data['created_date_from'] ?? null,
                'created_date_to' => $data['created_date_to'] ?? null,
            ];
            
            // Get languages
            $languages = $this->languageService->getAll();
            
            // Get total and filtered counts
            $totalRecords = $this->categoryRepositoryInterface->getCategoriesQuery([])->count();
            $filteredRecords = $this->categoryRepositoryInterface->getCategoriesQuery($filters)->count();
            
            // Determine sort column
            // Column 0 is 'index' (row number) - not sortable
            // Columns 1 to count($languages) are name translations
            // Then: department, active, created_at
            $orderBy = null;
            if ($orderColumnIndex >= 1 && $orderColumnIndex <= count($languages)) {
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
                $orderBy = 'department';
            } elseif ($orderColumnIndex == count($languages) + 2) {
                $orderBy = 'active';
            } elseif ($orderColumnIndex == count($languages) + 3) {
                $orderBy = 'created_at';
            } else {
                // Default sorting
                $orderBy = 'id';
            }
            
            Log::info('CategoryAction - Determined Sort', [
                'orderBy' => $orderBy,
                'orderDirection' => $orderDirection,
                'languagesCount' => count($languages)
            ]);
            
            // Get categories with pagination and sorting
            $categoriesQuery = $this->categoryRepositoryInterface->getCategoriesQuery($filters, $orderBy, $orderDirection);
            $categories = $categoriesQuery->paginate($perPage, ['*'], 'page', $page);
            
            // Return raw data - rendering will be handled by DataTables in the view
            $data = [];
            foreach ($categories as $index => $category) {
                $rowData = [
                    'index' => $index + 1,
                    'id' => $category->id,
                    'translations' => [],
                    'department' => null,
                    'active' => $category->active,
                    'created_at' => $category->created_at->format('Y-m-d H:i'),
                ];
                
                // Add translations for each language
                foreach ($languages as $language) {
                    $translation = $category->translations->where('lang_id', $language->id)
                        ->where('lang_key', 'name')
                        ->first();
                    $rowData['translations'][$language->code] = [
                        'name' => $translation ? $translation->lang_value : '-',
                        'rtl' => $language->rtl
                    ];
                }
                
                // Add department info
                if ($category->department) {
                    $rowData['department'] = [
                        'id' => $category->department->id,
                        'name' => $category->department->getTranslation('name', app()->getLocale())
                    ];
                }
                
                // Add first translation name for delete modal
                $firstTranslation = $category->translations->where('lang_key', 'name')->first();
                $rowData['first_name'] = $firstTranslation ? $firstTranslation->lang_value : '';
                
                $data[] = $rowData;
            }
            
            return [
                'data' => $data,
                'totalRecords' => $totalRecords,
                'filteredRecords' => $filteredRecords,
                'dataPaginated' => $categories
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in CategoryAction getDataTable: ' . $e->getMessage());
            return [
                'data' => [],
                'totalRecords' => 0,
                'filteredRecords' => 0,
                'dataPaginated' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ];
        }
    }
        
}
