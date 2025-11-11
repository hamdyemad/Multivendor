<?php

namespace App\Actions;

use App\Models\UserType;
use App\Services\AdminService;
use App\Services\LanguageService;
use Illuminate\Http\Request;

class AdminAction
{
    public function __construct(
        protected AdminService $adminService,
        protected LanguageService $languageService
    ) {
    }

    /**
     * Get admins data for DataTables AJAX
     */
    public function datatable(Request $request)
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
        $orderDirection = $request->get('order')[0]['dir'] ?? 'desc';

        // Get filter parameters
        $active = $request->get('active');
        $roleId = $request->get('role_id');
        $dateFrom = $request->get('created_date_from');
        $dateTo = $request->get('created_date_to');

        // Prepare filters array
        $filters = [
            'search' => $searchValue,
            'active' => $active,
            'role_id' => $roleId,
            'created_date_from' => $dateFrom,
            'created_date_to' => $dateTo,
        ];

        // Get languages
        $languages = $this->languageService->getAll();

        // Get total records before filtering
        $totalRecords = $this->adminService->getAdminsQuery([])->count();

        // Get admins with filters
        $baseQuery = $this->adminService->getAdminsQuery($filters);
        $filteredRecords = clone($baseQuery);
        $filteredRecords = $filteredRecords->count();

        // Prepare sorting parameters
        $orderBy = $this->determineSorting($request, $languages, $orderColumnIndex);

        // Get admins with sorting applied
        $query = $this->adminService->getAdminsQuery($filters, $orderBy, $orderDirection);

        // Apply pagination
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        // Start Permessions for roles
        switch (auth()->user()->user_type_id) {
            case UserType::SUPER_ADMIN_TYPE:
                $query->superAdminShow();
                break;
            case UserType::ADMIN_TYPE:
                $query->adminShow();
                break;
            case UserType::VENDOR_TYPE:
                $query->vendorShow();
                break;
            case UserType::VENDOR_USER_TYPE:
                $query->otherShow();
                break;
        }
        // End Permessions for roles

        $admins = $query->paginate($perPage, ['*'], 'page', $page);

        // Format data as arrays for DataTables
        $data = $this->formatDataForDataTables($admins, $languages);

        return response()->json([
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'current_page' => $admins->currentPage(),
            'last_page' => $admins->lastPage(),
            'per_page' => $admins->perPage(),
            'total' => $admins->total(),
            'from' => $admins->firstItem(),
            'to' => $admins->lastItem()
        ]);
    }

    /**
     * Determine sorting parameters based on request
     */
    protected function determineSorting(Request $request, $languages, int $orderColumnIndex)
    {
        $orderBy = null;
        $sortBy = $request->get('sort_by');

        if ($sortBy) {
            if (strpos($sortBy, 'name_') === 0) {
                $languageId = str_replace('name_', '', $sortBy);
                $orderBy = ['lang_id' => $languageId];
            } else {
                $orderBy = $sortBy;
            }
        } else {
            if ($orderColumnIndex >= 1 && $orderColumnIndex <= count($languages)) {
                $languageIndex = $orderColumnIndex - 1;
                $language = $languages[$languageIndex];
                $orderBy = ['lang_id' => $language->id];
            } else {
                $orderColumns = [
                    0 => 'id',
                    (count($languages) + 1) => 'email',
                    (count($languages) + 2) => 'active',
                    (count($languages) + 3) => 'created_at',
                ];

                if (isset($orderColumns[$orderColumnIndex])) {
                    $orderBy = $orderColumns[$orderColumnIndex];
                }
            }
        }

        return $orderBy;
    }

    /**
     * Format data for DataTables response - Returns raw data only
     */
    protected function formatDataForDataTables($admins, $languages)
    {
        $data = [];

        foreach ($admins as $admin) {
            $row = [];

            // ID
            $row['id'] = $admin->id;

            // Names for each language
            $row['names'] = [];
            foreach ($languages as $language) {
                $translation = $admin->translations()
                    ->where('lang_id', $language->id)
                    ->where('lang_key', 'name')
                    ->first();

                $row['names'][$language->id] = [
                    'value' => $translation ? $translation->lang_value : '-',
                    'rtl' => $language->rtl
                ];
            }

            // Email
            $row['email'] = $admin->email;

            // Role
            $row['role'] = $admin->roles->isNotEmpty()
                ? $admin->roles->first()->getTranslation('name', app()->getLocale())
                : '-';

            // Active Status
            $row['active'] = $admin->active ?? true;

            // Created At
            $row['created_at'] = $admin->created_at ? $admin->created_at : '-';

            // Admin name for delete modal
            $nameTranslation = $admin->translations()->where('lang_key', 'name')->first();
            $row['display_name'] = $nameTranslation && $nameTranslation->lang_value
                ? $nameTranslation->lang_value
                : 'Admin';

            $data[] = $row;
        }

        return $data;
    }
}
