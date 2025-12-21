<?php

namespace App\Actions;

use App\Models\UserType;
use App\Services\VendorUserService;
use App\Services\LanguageService;
use Illuminate\Http\Request;

class VendorUserAction
{
    public function __construct(
        protected VendorUserService $vendorUserService,
        protected LanguageService $languageService
    ) {
    }

    /**
     * Get vendor users data for DataTables AJAX
     */
    public function datatable(Request $request)
    {
        // Get search value
        $searchValue = $request->get('search');
        if (is_array($searchValue)) {
            $searchValue = $searchValue['value'] ?? '';
        }

        // Get filter parameters
        $active = $request->get('active');
        $roleId = $request->get('role_id');
        $vendorId = $request->get('vendor_id');
        $dateFrom = $request->get('created_date_from');
        $dateTo = $request->get('created_date_to');

        // Prepare filters array
        $filters = [
            'search' => $searchValue,
            'active' => $active,
            'role_id' => $roleId,
            'vendor_id' => $vendorId,
            'created_date_from' => $dateFrom,
            'created_date_to' => $dateTo,
        ];

        // Get languages
        $languages = $this->languageService->getAll();

        // Get total records before filtering
        $totalRecords = $this->vendorUserService->getVendorUsersQuery([])->count();

        // Get query with filters
        $query = $this->vendorUserService->getVendorUsersQuery($filters);
        
        // Use paginate for DataTables
        $perPage = $request->get('length', 10);
        $page = ($request->get('start', 0) / $perPage) + 1;

        $users = $query->paginate($perPage, ['*'], 'page', $page);

        // Format data
        $data = $this->formatDataForDataTables($users, $languages);

        return response()->json([
            'draw' => intval($request->get('draw', 1)),
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $users->total(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'per_page' => $users->perPage(),
            'total' => $users->total(),
            'from' => $users->firstItem(),
            'to' => $users->lastItem()
        ]);
    }

    /**
     * Format data for DataTables response
     */
    protected function formatDataForDataTables($users, $languages)
    {
        $data = [];

        foreach ($users as $user) {
            $row = [];

            // ID
            $row['id'] = $user->id;

            // Names for each language
            $row['names'] = [];
            foreach ($languages as $language) {
                $translation = $user->translations()
                    ->where('lang_id', $language->id)
                    ->where('lang_key', 'name')
                    ->first();

                $row['names'][$language->id] = [
                    'value' => $translation ? $translation->lang_value : '-',
                    'rtl' => $language->rtl
                ];
            }

            // Email
            $row['email'] = $user->email;

            // Vendor
            $row['vendor'] = $user->vendorById ? $user->vendorById->getTranslation('name', app()->getLocale()) : '-';

            // Role
            $row['role'] = $user->roles->isNotEmpty()
                ? $user->roles->first()->getTranslation('name', app()->getLocale())
                : '-';

            // Active Status
            $row['active'] = $user->active ?? true;
            $row['block'] = $user->block ?? false;

            // Created At
            $row['created_at'] = $user->created_at ? $user->created_at->format('Y-m-d H:i') : '-';

            // Display name for delete modal
            $nameTranslation = $user->translations()->where('lang_key', 'name')->first();
            $row['display_name'] = $nameTranslation && $nameTranslation->lang_value
                ? $nameTranslation->lang_value
                : 'User';

            $data[] = $row;
        }

        return $data;
    }
}
