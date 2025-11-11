<?php

namespace Modules\AreaSettings\app\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\AreaSettings\app\Services\RegionService;
use App\Services\LanguageService;

class RegionAction
{
    public function __construct(
        protected RegionService $regionService,
        protected LanguageService $languageService
    ) {}

    /**
     * Get datatable data for regions
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
        $cityId = $request->get('city_id');
        $active = $request->get('active');
        $dateFrom = $request->get('created_date_from');
        $dateTo = $request->get('created_date_to');

        // Prepare filters array
        $filters = [
            'search' => $searchValue,
            'country_id' => $countryId,
            'city_id' => $cityId,
            'active' => $active,
            'created_date_from' => $dateFrom,
            'created_date_to' => $dateTo,
        ];

        // Get languages
        $languages = $this->languageService->getAll();

        // Get total records before filtering
        $totalRecords = $this->regionService->getRegionsQuery([])->count();

        // Get regions with filters - Clone query for counting
        $baseQuery = $this->regionService->getRegionsQuery($filters);
        $filteredRecords = clone($baseQuery);
        $filteredRecords = $filteredRecords->count();

        // Prepare sorting parameters
        $orderBy = $this->prepareSorting($request, $orderColumnIndex, $orderDirection, $languages);

        // Get regions with sorting applied
        $sortedQuery = $this->regionService->getRegionsQuery($filters, $orderBy, $orderDirection);

        // Apply pagination
        $perPage = $request->get('per_page', $request->get('length', 15));
        $page = $request->get('page', 1);
        $regions = $sortedQuery->paginate($perPage, ['*'], 'page', $page);

        // Format data for DataTables
        $data = $this->formatDataForDataTables($regions, $languages);

        return [
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'current_page' => $regions->currentPage(),
            'last_page' => $regions->lastPage(),
            'per_page' => $regions->perPage(),
            'total' => $regions->total(),
            'from' => $regions->firstItem(),
            'to' => $regions->lastItem()
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
                (count($languagesArray) + 1) => 'city_id',
                (count($languagesArray) + 2) => 'id', // subregions count (not sortable)
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
    protected function formatDataForDataTables($regions, $languages)
    {
        $data = [];
        $startIndex = ($regions->currentPage() - 1) * $regions->perPage();

        foreach ($regions as $index => $region) {
            $row = [
                'index' => $startIndex + $index + 1,
                'id' => $region->id,
                'names' => [],
                'city' => [
                    'id' => $region->city_id,
                    'name' => $region->city ? $region->city->getTranslation('name', app()->getLocale()) : '-'
                ],
                'subregions_count' => $region->subregions()->count(),
                'active' => $region->active ?? true,
                'created_at' => $region->created_at,
                'display_name' => ''
            ];

            // Get names for each language
            foreach ($languages as $language) {
                $name = $region->getTranslation('name', $language->code) ?? '-';
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
