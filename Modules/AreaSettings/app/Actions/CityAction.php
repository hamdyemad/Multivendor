<?php

namespace Modules\AreaSettings\app\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\AreaSettings\app\Services\CityService;
use App\Services\LanguageService;

class CityAction
{
    public function __construct(
        protected CityService $cityService,
        protected LanguageService $languageService
    ) {}

    /**
     * Get datatable data for cities
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

        // Get filter parameters
        $countryId = $request->get('country_id');
        $active = $request->get('active');
        $dateFrom = $request->get('created_date_from');
        $dateTo = $request->get('created_date_to');

        // Prepare filters array
        $filters = [
            'search' => $searchValue,
            'country_id' => $countryId,
            'active' => $active,
            'created_date_from' => $dateFrom,
            'created_date_to' => $dateTo,
        ];

        // Get languages
        $languages = $this->languageService->getAll();

        // Get total records before filtering
        $totalRecords = $this->cityService->getCitiesQuery([])->count();

        // Get cities with filters - Clone query for counting
        $baseQuery = $this->cityService->getCitiesQuery($filters);
        $filteredRecords = clone($baseQuery);
        $filteredRecords = $filteredRecords->count();

        // Prepare sorting parameters
        $orderBy = $this->prepareSorting($request, $orderColumnIndex, $orderDirection, $languages);

        // Get cities with sorting applied
        $sortedQuery = $this->cityService->getCitiesQuery($filters, $orderBy, $orderDirection);

        // Apply pagination
        $perPage = $request->get('per_page', $request->get('length', 15));
        $page = $request->get('page', 1);
        $cities = $sortedQuery->paginate($perPage, ['*'], 'page', $page);

        // Format data for DataTables
        $data = $this->formatDataForDataTables($cities, $languages);

        return [
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'current_page' => $cities->currentPage(),
            'last_page' => $cities->lastPage(),
            'per_page' => $cities->perPage(),
            'total' => $cities->total(),
            'from' => $cities->firstItem(),
            'to' => $cities->lastItem()
        ];
    }

    /**
     * Prepare sorting parameters
     */
    protected function prepareSorting(Request $request, int $orderColumnIndex, string $orderDirection, $languages)
    {
        $orderBy = null;
        $languagesArray = $languages->values()->all();

        // Check if sorting by name column (columns 1 to count($languages))
        // Note: Column 0 is 'index', so names start at column 1
        if ($orderColumnIndex >= 1 && $orderColumnIndex <= count($languagesArray)) {
            // Sort by translated name - pass language ID to repository
            $languageIndex = $orderColumnIndex - 1;
            if (isset($languagesArray[$languageIndex])) {
                $language = $languagesArray[$languageIndex];
                $orderBy = ['lang_id' => $language->id];
                Log::info('Sorting by language name:', [
                    'language_id' => $language->id,
                    'language_code' => $language->code
                ]);
            }
        } else {
            // Sort by regular columns
            // Adjusted for 'index' column at position 0
            $orderColumns = [
                0 => null, // index column - not sortable
                (count($languagesArray) + 1) => 'country_id',
                (count($languagesArray) + 2) => 'id', // regions count (not sortable by query)
                (count($languagesArray) + 3) => 'active',
                (count($languagesArray) + 4) => 'created_at',
            ];

            if (isset($orderColumns[$orderColumnIndex]) && $orderColumns[$orderColumnIndex] !== null) {
                $orderBy = $orderColumns[$orderColumnIndex];
            }
        }

        return $orderBy;
    }

    /**
     * Format data for DataTables (raw data only, no HTML)
     */
    protected function formatDataForDataTables($cities, $languages)
    {
        $data = [];
        $startIndex = ($cities->currentPage() - 1) * $cities->perPage();

        foreach ($cities as $index => $city) {
            $row = [
                'index' => $startIndex + $index + 1,
                'id' => $city->id,
                'names' => [],
                'country' => [
                    'id' => $city->country_id,
                    'name' => $city->country ? $city->country->getTranslation('name', app()->getLocale()) : '-'
                ],
                'regions_count' => $city->regions()->count(),
                'active' => $city->active ?? true,
                'created_at' => $city->created_at,
                'display_name' => ''
            ];

            // Get names for each language
            foreach ($languages as $language) {
                $name = $city->getTranslation('name', $language->code) ?? '-';
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
