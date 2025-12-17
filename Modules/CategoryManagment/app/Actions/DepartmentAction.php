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

            Log::info('Department Action - Sorting Debug', [
                'orderColumnIndex' => $orderColumnIndex,
                'orderDirection' => $orderDirection,
                'languagesCount' => count($languages),
                'languages' => $languages->pluck('name', 'id')->toArray(),
                'columnMapping' => [
                    '0' => 'ID',
                    '1' => 'First Language (' . ($languages->first() ? $languages->first()->name : 'N/A') . ')',
                    '2' => 'Second Language (' . ($languages->count() > 1 ? $languages->skip(1)->first()->name : 'N/A') . ')',
                    (count($languages) + 1) => 'Active Status',
                    (count($languages) + 2) => 'Created At',
                    (count($languages) + 3) => 'Actions'
                ]
            ]);

            if ($orderColumnIndex == 0) {
                $orderBy = 'id';
                Log::info('Department Action - Sorting by ID');
            } elseif ($orderColumnIndex >= 1 && $orderColumnIndex <= count($languages)) {
                // Sorting by translated name column (after ID column)
                $languageIndex = $orderColumnIndex - 1;
                $selectedLanguage = $languages->values()->get($languageIndex);
                if ($selectedLanguage) {
                    $orderBy = [
                        'lang_id' => $selectedLanguage->id,
                        'key' => 'name'
                    ];
                }
            } elseif ($orderColumnIndex == count($languages) + 1) {
                $orderBy = 'commission';
                Log::info('Department Action - Sorting by Commission');
            } elseif ($orderColumnIndex == count($languages) + 2) {
                $orderBy = 'active';
                Log::info('Department Action - Sorting by Active');
            } elseif ($orderColumnIndex == count($languages) + 3) {
                $orderBy = 'created_at';
                Log::info('Department Action - Sorting by Created At');
            }

            Log::info('Department Action - Final Sort Config', ['orderBy' => $orderBy]);

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
