<?php

namespace Modules\SystemSetting\app\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\SystemSetting\app\Services\CurrencyService;
use App\Services\LanguageService;

class CurrencyAction
{
    public function __construct(
        protected CurrencyService $currencyService,
        protected LanguageService $languageService
    ) {}

    /**
     * Get datatable data for currencies
     */
    public function getDatatableData(Request $request)
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);

        // Get search value
        $searchValue = $request->get('search');
        if (is_array($searchValue)) {
            $searchValue = $searchValue['value'] ?? '';
        }

        $orderColumnIndex = $request->get('order')[0]['column'] ?? 0;
        $orderDirection = $request->get('order')[0]['dir'] ?? 'asc';

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
        $totalRecords = $this->currencyService->getCurrenciesQuery([])->count();

        // Get currencies with filters
        $baseQuery = $this->currencyService->getCurrenciesQuery($filters);
        $filteredRecords = clone($baseQuery);
        $filteredRecords = $filteredRecords->count();

        // Prepare sorting parameters
        $orderBy = $this->prepareSorting($request, $orderColumnIndex, $orderDirection, $languages);

        // Get currencies with sorting applied
        $sortedQuery = $this->currencyService->getCurrenciesQuery($filters, $orderBy, $orderDirection);

        // Apply pagination
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $currencies = $sortedQuery->paginate($perPage, ['*'], 'page', $page);

        // Format data for DataTables
        $data = $this->formatDataForDataTables($currencies, $languages);

        return [
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'current_page' => $currencies->currentPage(),
            'last_page' => $currencies->lastPage(),
            'per_page' => $currencies->perPage(),
            'total' => $currencies->total(),
            'from' => $currencies->firstItem(),
            'to' => $currencies->lastItem()
        ];
    }

    /**
     * Prepare sorting parameters
     */
    protected function prepareSorting(Request $request, int $orderColumnIndex, string $orderDirection, $languages)
    {
        $orderBy = null;
        $sortBy = $request->get('sort_by');

        $languagesArray = $languages->values()->all();

        if ($sortBy) {
            if (strpos($sortBy, 'name_') === 0) {
                $languageId = str_replace('name_', '', $sortBy);
                $orderBy = ['lang_id' => $languageId];
            } else {
                $orderBy = $sortBy;
            }
        } else {
            if ($orderColumnIndex >= 1 && $orderColumnIndex <= count($languagesArray)) {
                $languageIndex = $orderColumnIndex - 1;
                if (isset($languagesArray[$languageIndex])) {
                    $language = $languagesArray[$languageIndex];
                    $orderBy = ['lang_id' => $language->id];
                }
            } else {
                $orderColumns = [
                    0 => null,
                    (count($languagesArray) + 1) => 'code',
                    (count($languagesArray) + 2) => 'symbol',
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
     * Format data for DataTables
     */
    protected function formatDataForDataTables($currencies, $languages)
    {
        $data = [];
        $startIndex = ($currencies->currentPage() - 1) * $currencies->perPage();

        foreach ($currencies as $index => $currency) {
            $row = [
                'index' => $startIndex + $index + 1,
                'id' => $currency->id,
                'names' => [],
                'code' => $currency->code,
                'active' => $currency->active ?? true,
                'created_at' =>  $currency->created_at,
                'display_name' => ''
            ];

            // Get names for each language
            foreach ($languages as $language) {
                $translation = $currency->translations()
                    ->where('lang_id', $language->id)
                    ->where('lang_key', 'name')
                    ->first();

                $name = $translation ? $translation->lang_value : '-';
                $row['names'][$language->id] = [
                    'value' => $name,
                    'rtl' => $language->rtl
                ];

                if (!$row['display_name'] && $name !== '-') {
                    $row['display_name'] = $name;
                }
            }

            $data[] = $row;
        }

        return $data;
    }
}
