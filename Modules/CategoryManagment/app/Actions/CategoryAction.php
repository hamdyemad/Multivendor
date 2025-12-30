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
            // Get pagination parameters from DataTables
            $perPage = isset($data['length']) && $data['length'] > 0 ? (int)$data['length'] : 10;
            $start = isset($data['start']) && $data['start'] >= 0 ? (int)$data['start'] : 0;
            // Calculate page number from start offset
            $page = $perPage > 0 ? floor($start / $perPage) + 1 : 1;

            // Get custom sorting parameters
            $sortColumn = $data['sort_column'] ?? 'sort_number';
            $sortDirection = $data['sort_direction'] ?? 'asc';

            Log::info('CategoryAction - Sorting Parameters', [
                'sortColumn' => $sortColumn,
                'sortDirection' => $sortDirection,
            ]);

            // Get filter parameters
            $filters = [
                'search' => $data['search'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'active' => $data['active'] ?? null,
                'view_status' => $data['view_status'] ?? null,
                'created_date_from' => $data['created_date_from'] ?? null,
                'created_date_to' => $data['created_date_to'] ?? null,
            ];

            // Get languages
            $languages = $this->languageService->getAll();

            // Get total and filtered counts
            $totalRecords = $this->categoryRepositoryInterface->getCategoriesQuery([])->count();
            $filteredRecords = $this->categoryRepositoryInterface->getCategoriesQuery($filters)->count();

            // Determine sort column
            $orderBy = $sortColumn;
            $orderDirection = $sortDirection;

            Log::info('CategoryAction - Determined Sort', [
                'orderBy' => $orderBy,
                'orderDirection' => $orderDirection,
            ]);

            // Get categories with pagination and sorting
            $categoriesQuery = $this->categoryRepositoryInterface->getCategoriesQuery($filters, $orderBy, $orderDirection);
            $categories = $categoriesQuery->paginate($perPage, ['*'], 'page', $page);

            // Return raw data - rendering will be handled by DataTables in the view
            $data = [];
            $index = $start + 1; // Start index from the correct offset
            foreach ($categories as $category) {
                $rowData = [
                    'index' => $index++,
                    'id' => $category->id,
                    'translations' => [],
                    'department' => null,
                    'sort_number' => $category->sort_number,
                    'view_status' => $category->view_status,
                    'active' => $category->active,
                    'created_at' => $category->created_at,
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
