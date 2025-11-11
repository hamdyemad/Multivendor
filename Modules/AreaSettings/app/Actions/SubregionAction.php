<?php

namespace Modules\AreaSettings\app\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\AreaSettings\app\Services\SubRegionService;
use App\Services\LanguageService;

class SubRegionAction
{
    public function __construct(
        protected SubRegionService $subregionService,
        protected LanguageService $languageService
    ) {}

    /**
     * Get datatable data for subregions
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

        // Get sorting parameters - handle both DataTables default format and custom parameters
        $orderColumnIndex = $request->get('order_column');
        $orderDirection = $request->get('order_dir', 'desc');

        if ($orderColumnIndex === null && $request->has('order')) {
            $orderData = $request->get('order');
            if (is_array($orderData) && isset($orderData[0])) {
                $orderColumnIndex = $orderData[0]['column'] ?? 0;
                $orderDirection = $orderData[0]['dir'] ?? 'desc';
            }
        }

        $orderColumnIndex = $orderColumnIndex ?? 0;

        // Get filter parameters
        $regionId = $request->get('region_id');
        $active = $request->get('active');
        $dateFrom = $request->get('created_date_from');
        $dateTo = $request->get('created_date_to');

        // Prepare filters array
        $filters = [
            'search' => $searchValue,
            'region_id' => $regionId,
            'active' => $active,
            'created_date_from' => $dateFrom,
            'created_date_to' => $dateTo,
        ];

        // Debug logging
        Log::info('SubRegion Datatable Filters:', [
            'filters' => $filters,
            'order_column' => $orderColumnIndex,
            'order_dir' => $orderDirection
        ]);

        // Get languages
        $languages = $this->languageService->getAll();

        // Get total records before filtering
        $totalRecords = $this->subregionService->getSubRegionsQuery([])->count();

        // Get subregions with filters - Clone query for counting
        $baseQuery = $this->subregionService->getSubRegionsQuery($filters);
        $filteredRecords = clone($baseQuery);
        $filteredRecords = $filteredRecords->count();

        // Prepare sorting parameters
        $orderBy = $this->prepareSorting($request, $orderColumnIndex, $orderDirection, $languages);

        // Get sub-regions with sorting applied
        $sortedQuery = $this->subregionService->getSubRegionsQuery($filters, $orderBy, $orderDirection);

        // Apply pagination
        $perPage = $request->get('per_page', $request->get('length', 15));
        $page = $request->get('page', 1);
        $subregions = $sortedQuery->paginate($perPage, ['*'], 'page', $page);

        // Format data for DataTables
        $data = $this->formatDataForDataTables($subregions, $languages);

        return [
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'current_page' => $subregions->currentPage(),
            'last_page' => $subregions->lastPage(),
            'per_page' => $subregions->perPage(),
            'total' => $subregions->total(),
            'from' => $subregions->firstItem(),
            'to' => $subregions->lastItem()
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
                (count($languagesArray) + 1) => 'region_id',
                (count($languagesArray) + 2) => 'active',
                (count($languagesArray) + 3) => 'created_at',
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
    protected function formatDataForDataTables($subregions, $languages)
    {
        $data = [];
        $startIndex = ($subregions->currentPage() - 1) * $subregions->perPage();

        foreach ($subregions as $index => $subregion) {
            $row = [
                'index' => $startIndex + $index + 1,
                'id' => $subregion->id,
                'names' => [],
                'region' => [
                    'id' => $subregion->region_id,
                    'name' => $subregion->region ? $subregion->region->getTranslation('name', app()->getLocale()) : '-'
                ],
                'active' => $subregion->active ?? true,
                'created_at' => $subregion->created_at,
                'display_name' => ''
            ];

            // Get names for each language
            foreach ($languages as $language) {
                $name = $subregion->getTranslation('name', $language->code) ?? '-';
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
