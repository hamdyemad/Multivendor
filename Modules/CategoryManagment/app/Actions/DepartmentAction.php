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
            $totalRecords = $this->departmentService->getDepartmentsQuery([])->count();
            $filteredRecords = $this->departmentService->getDepartmentsQuery($filters)->count();
            
            // Determine sort column
            $orderBy = null;
            if ($orderColumnIndex == 0) {
                $orderBy = 'id';
            } elseif ($orderColumnIndex >= 2 && $orderColumnIndex <= count($languages) + 1) {
                // Sorting by translated name column (after ID and Image columns)
                $languageIndex = $orderColumnIndex - 2;
                $selectedLanguage = $languages->values()->get($languageIndex);
                if ($selectedLanguage) {
                    $orderBy = [
                        'lang_id' => $selectedLanguage->id,
                        'key' => 'name'
                    ];
                }
            } elseif ($orderColumnIndex == count($languages) + 2) {
                $orderBy = 'active';
            } elseif ($orderColumnIndex == count($languages) + 3) {
                $orderBy = 'created_at';
            }
            
            // Get departments with pagination and sorting
            $departmentsQuery = $this->departmentService->getDepartmentsQuery($filters, $orderBy, $orderDirection);
            $departments = $departmentsQuery->paginate($perPage, ['*'], 'page', $page);
            
            // Return raw data - rendering will be handled by DataTables in the view
            $data = [];
            foreach ($departments as $index => $department) {
                $rowData = [
                    'id' => $index + 1,
                    'department_id' => $department->id,
                    'image' => $department->image,
                    'translations' => [],
                    'active' => $department->active,
                    'created_at' => $department->created_at->format('Y-m-d H:i'),
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
