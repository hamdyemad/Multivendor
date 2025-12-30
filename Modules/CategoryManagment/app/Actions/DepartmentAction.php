<?php

namespace Modules\CategoryManagment\app\Actions;

use Modules\CategoryManagment\app\Services\DepartmentService;
use App\Services\LanguageService;
use App\Traits\Res;
use Illuminate\Support\Facades\Log;

class DepartmentAction {

    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected DepartmentService $departmentService
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

            // Get filter parameters
            $filters = [
                'search' => $data['search'] ?? null,
                'active' => isset($data['active']) && $data['active'] !== '' ? $data['active'] : null,
                'view_status' => isset($data['view_status']) && $data['view_status'] !== '' ? $data['view_status'] : null,
                'created_date_from' => $data['created_date_from'] ?? null,
                'created_date_to' => $data['created_date_to'] ?? null,
            ];

            // Get languages
            $languages = $this->languageService->getAll();

            // Get total and filtered counts
            $totalRecords = $this->departmentService->getDepartmentsQuery([])->count();
            $filteredRecords = $this->departmentService->getDepartmentsQuery($filters)->count();

            // Determine sort column
            $orderBy = $sortColumn;
            $orderDirection = $sortDirection;

            Log::info('Department Action - Sorting Debug', [
                'sortColumn' => $sortColumn,
                'sortDirection' => $sortDirection,
            ]);

            // Get departments with pagination and sorting
            $departmentsQuery = $this->departmentService->getDepartmentsQuery($filters, $orderBy, $orderDirection);
            $departments = $departmentsQuery->paginate($perPage, ['*'], 'page', $page);

            // Return raw data - rendering will be handled by DataTables in the view
            $data = [];
            $startIndex = ($page - 1) * $perPage + 1; // Calculate starting index for current page

            foreach ($departments as $index => $department) {
                $rowData = [
                    'id' => $startIndex + $index,
                    'department_id' => $department->id,
                    'image' => $department->image,
                    'commission' => $department->commission,
                    'sort_number' => $department->sort_number ?? 0,
                    'view_status' => $department->view_status ?? 1,
                    'translations' => [],
                    'active' => $department->active,
                    'created_at' => $department->created_at,
                ];

                // Add translations for each language
                foreach ($languages as $language) {
                    $translation = $department->translations->where('lang_id', $language->id)
                        ->where('lang_key', 'name')
                        ->first();
                    $rowData['translations'][$language->code] = [
                        'name' => $translation ? $translation->lang_value : '-',
                        'rtl' => $language->rtl
                    ];
                }

                // Add first translation name for delete modal
                $firstTranslation = $department->translations->where('lang_key', 'name')->first();
                $rowData['first_name'] = $firstTranslation ? $firstTranslation->lang_value : '';

                $data[] = $rowData;
            }

            return [
                'data' => $data,
                'totalRecords' => $totalRecords,
                'filteredRecords' => $filteredRecords,
                'dataPaginated' => $departments
            ];

        } catch (\Exception $e) {
            Log::error('Error in DepartmentAction getDataTable: ' . $e->getMessage());
            return [
                'data' => [],
                'totalRecords' => 0,
                'filteredRecords' => 0,
                'dataPaginated' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10)
            ];
        }
    }

}
