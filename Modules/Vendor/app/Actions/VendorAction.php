<?php

namespace Modules\Vendor\app\Actions;
use App\Models\User;
use App\Services\LanguageService;
use App\Traits\Res;
use Modules\Vendor\app\Interfaces\VendorInterface;

class VendorAction {

    use Res;

    public function __construct(
        protected LanguageService $languageService,
        protected VendorInterface $vendorInterface
        )
    {

    }
   public function getDataTable($data) {
        $draw = $data['draw'];
        $start = $data['start'];
        $length = $data['length'];

        // Get search value from custom parameter or DataTables default
        $searchValue = $data['search'];
        if (is_array($searchValue)) {
            $searchValue = $searchValue['value'] ?? '';
        }

        $orderColumnIndex = $data['orderColumnIndex'];
        $orderDirection = $data['orderDirection'];

        // Get languages
        $languages = $this->languageService->getAll();

        // Prepare filters array
        $filters = [
            'search' => $searchValue,
            'active' => $data['active'] ?? '',
            'created_date_from' => $data['created_date_from'],
            'created_date_to' => $data['created_date_to']
        ];

        // Get total records before filtering
        $totalRecords = $this->vendorInterface->getQuery()->count();
        $baseQuery = $this->vendorInterface->getQuery($filters);
        // Get filtered count (clone query to avoid mutation)
        $filteredRecords = clone($baseQuery);
        $filteredRecords = $filteredRecords->count();
        $query = $baseQuery;

        // Clear existing orders to prevent conflicts with latest() in base query
        $query->reorder();

        // Apply sorting
        // Check if sorting by name column (columns 1 to count($languages))
        if ($orderColumnIndex >= 1 && $orderColumnIndex <= count($languages)) {
            // Get the language for this column
            $languageIndex = $orderColumnIndex - 1;
            $selectedLanguage = $languages->values()->get($languageIndex);

            // Join with translations table to sort by translated name
            $query->leftJoin('translations as trans_sort', function($join) use ($selectedLanguage) {
                $join->on('vendors.id', '=', 'trans_sort.translatable_id')
                     ->where('trans_sort.translatable_type', '=', 'Modules\\Vendor\\app\\Models\\Vendor')
                     ->where('trans_sort.lang_key', '=', 'name')
                     ->where('trans_sort.lang_id', '=', $selectedLanguage->id);
            })
            ->orderBy('trans_sort.lang_value', $orderDirection)
            ->select('vendors.*'); // Select only vendors columns to avoid conflicts
        } else {
            // Special handling for commission sorting (relationship)
            if ($orderColumnIndex === (count($languages) + 3)) {
                $query->leftJoin('vendor_commission', 'vendors.id', '=', 'vendor_commission.vendor_id')
                      ->orderBy('vendor_commission.commission', $orderDirection)
                      ->select('vendors.*');
            } else {
                // Build column map for non-translation columns
                $orderColumns = [
                    0 => 'id',
                    (count($languages) + 1) => 'id', // email (sortable via user relationship - using id for now)
                    (count($languages) + 2) => 'country_id', // country
                    (count($languages) + 4) => 'active', // active status
                    (count($languages) + 5) => 'created_at',
                ];

                if (isset($orderColumns[$orderColumnIndex])) {
                    $query->orderBy($orderColumns[$orderColumnIndex], $orderDirection);
                }
            }
        }

        // Apply pagination
        $perPage = $data['length'];
        $page = $data['page'];
        $vendors = $query->with(['translations', 'user', 'country', 'commission', 'logo'])->paginate($perPage, ['*'], 'page', $page);

        // Return raw data - rendering will be handled by DataTables in the view
        $tableData = [];
        foreach ($vendors as $index => $vendor) {
            $rowData = [
                'row_number' => ($vendors->currentPage() - 1) * $vendors->perPage() + $index + 1,
                'id' => $vendor->id,
                'translations' => [],
                'logo' => $vendor->logo ? asset('storage/' . $vendor->logo->path) : null,
                'email' => $vendor->user->email ?? '-',
                'country_name' => $vendor->country ? $vendor->country->getTranslation('name', app()->getLocale()) : '-',
                'commission' => ($vendor->commission) ? $vendor->commission->commission : 0,
                'active' => $vendor->active,
                'created_at' => $vendor->created_at,
            ];

            // Add translations for each language
            foreach ($languages as $language) {
                $name = $vendor->getTranslation('name', $language->code) ?? '-';
                $rowData['translations'][$language->code] = [
                    'name' => truncateString($name, 15),
                    'rtl' => $language->rtl
                ];
            }

            // Add first translation name for delete modal
            $firstTranslation = $vendor->translations->where('lang_key', 'name')->first();
            $rowData['first_name'] = $firstTranslation ? $firstTranslation->lang_value : '';

            $tableData[] = $rowData;
        }


        return [
            'dataPaginated' => $vendors,
            'data' => $tableData,
            'totalRecords' => $totalRecords,
            'filteredRecords' => $filteredRecords,
        ];
    }
}
