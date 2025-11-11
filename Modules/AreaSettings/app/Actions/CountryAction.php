<?php

namespace Modules\AreaSettings\app\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\AreaSettings\app\Services\CountryService;
use App\Services\LanguageService;

class CountryAction
{
    public function __construct(
        protected CountryService $countryService,
        protected LanguageService $languageService
    ) {}

    /**
     * Get datatable data for countries
     */
    public function getDatatableData(Request $request)
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);

        // Get search value from custom parameter or DataTables default
        $searchValue = $request->get('search');
        if (is_array($searchValue)) {
            $searchValue = $searchValue['value'] ?? '';
        }

        $orderColumnIndex = $request->get('order')[0]['column'] ?? 0;
        $orderDirection = $request->get('order')[0]['dir'] ?? 'asc';

        Log::info('DataTable Order:', [
            'column_index' => $orderColumnIndex,
            'direction' => $orderDirection
        ]);

        // Get filter parameters
        $active = $request->get('active');
        $dateFrom = $request->get('created_date_from');
        $dateTo = $request->get('created_date_to');

        // Prepare filters array
        $filters = [
            'search' => $searchValue,
            'active' => $active,
            'created_date_from' => $dateFrom,
            'created_date_to' => $dateTo,
        ];

        // Get languages
        $languages = $this->languageService->getAll();

        // Get total records before filtering
        $totalRecords = $this->countryService->getCountriesQuery([])->count();

        // Get countries with filters - Clone query for counting
        $baseQuery = $this->countryService->getCountriesQuery($filters);
        $filteredRecords = clone($baseQuery);
        $filteredRecords = $filteredRecords->count();

        // Prepare sorting parameters
        $orderBy = $this->prepareSorting($request, $orderColumnIndex, $orderDirection, $languages);

        // Get countries with sorting applied
        $sortedQuery = $this->countryService->getCountriesQuery($filters, $orderBy, $orderDirection);

        // Apply pagination
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $countries = $sortedQuery->paginate($perPage, ['*'], 'page', $page);

        // Format data for DataTables
        $data = $this->formatDataForDataTables($countries, $languages);

        return [
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'current_page' => $countries->currentPage(),
            'last_page' => $countries->lastPage(),
            'per_page' => $countries->perPage(),
            'total' => $countries->total(),
            'from' => $countries->firstItem(),
            'to' => $countries->lastItem()
        ];
    }

    /**
     * Prepare sorting parameters
     */
    protected function prepareSorting(Request $request, int $orderColumnIndex, string $orderDirection, $languages)
    {
        $orderBy = null;
        $sortBy = $request->get('sort_by');
        $sortDirection = $request->get('sort_direction', 'asc');

        // Convert languages collection to array for index access
        $languagesArray = $languages->values()->all();

        if ($sortBy) {
            // Handle name sorting by language ID
            if (strpos($sortBy, 'name_') === 0) {
                $languageId = str_replace('name_', '', $sortBy);
                $orderBy = ['lang_id' => $languageId];
            } else {
                // Handle regular column sorting
                $orderBy = $sortBy;
            }
        } else {
            // Fallback to DataTables format if no sort_by parameter
            // Check if sorting by name column (columns 1 to count($languages))
            // Note: Column 0 is now 'index', so names start at column 1
            if ($orderColumnIndex >= 1 && $orderColumnIndex <= count($languagesArray)) {
                // Sort by translated name - pass language ID to repository
                $languageIndex = $orderColumnIndex - 1;
                if (isset($languagesArray[$languageIndex])) {
                    $language = $languagesArray[$languageIndex];
                    $orderBy = ['lang_id' => $language->id];
                    Log::info('Sorting by language name:', [
                        'language_id' => $language->id,
                        'language_code' => $language->code,
                        'language_name' => $language->name
                    ]);
                }
            } else {
                // Sort by regular columns
                // Adjusted for 'index' column at position 0
                $orderColumns = [
                    0 => null, // index column - not sortable
                    (count($languagesArray) + 1) => 'code',
                    (count($languagesArray) + 2) => 'phone_code',
                    (count($languagesArray) + 3) => 'active',
                    (count($languagesArray) + 4) => 'created_at',
                ];

                if (isset($orderColumns[$orderColumnIndex]) && $orderColumns[$orderColumnIndex] !== null) {
                    $orderBy = $orderColumns[$orderColumnIndex];
                }
            }
        }

        return $orderBy;
    }

    /**
     * Format data for DataTables (raw data only, no HTML)
     */
    protected function formatDataForDataTables($countries, $languages)
    {
        $data = [];
        $startIndex = ($countries->currentPage() - 1) * $countries->perPage();

        foreach ($countries as $index => $country) {
            $row = [
                'index' => $startIndex + $index + 1,
                'id' => $country->id,
                'names' => [],
                'code' => $country->code,
                'phone_code' => $country->phone_code,
                'active' => $country->active ?? true,
                'created_at' =>  $country->created_at,
                'display_name' => ''
            ];

            // Get names for each language
            foreach ($languages as $language) {
                $translation = $country->translations()
                    ->where('lang_id', $language->id)
                    ->where('lang_key', 'name')
                    ->first();

                $name = $translation ? $translation->lang_value : '-';
                $row['names'][$language->id] = [
                    'value' => $name,
                    'rtl' => $language->rtl
                ];

                // Set display name (first available translation)
                if (!$row['display_name'] && $name !== '-') {
                    $row['display_name'] = $name;
                }
            }

            $data[] = $row;
        }

        return $data;
    }
}
