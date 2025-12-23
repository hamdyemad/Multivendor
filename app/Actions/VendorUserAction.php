<?php

namespace App\Actions;

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

        // Get total records before filtering (but still respecting vendor filter for vendor users)
        // Pass empty filters but the repository will still apply vendor filter for isVendor() users
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

            // Names for each language with code
            $row['names'] = [];
            foreach ($languages as $language) {
                $translation = $user->translations()
                    ->where('lang_id', $language->id)
                    ->where('lang_key', 'name')
                    ->first();

                $row['names'][$language->id] = [
                    'value' => $translation ? $translation->lang_value : '-',
                    'rtl' => $language->rtl,
                    'code' => $language->code
                ];
            }

            // Email
            $row['email'] = $user->email;

            // Vendor
            $row['vendor'] = $user->vendorById ? $user->vendorById->getTranslation('name', app()->getLocale()) : '-';

            // Roles as Badges
            if ($user->roles->isNotEmpty()) {
                $rolesHtml = '';
                foreach ($user->roles as $role) {
                    $roleName = $role->getTranslation('name', app()->getLocale());
                    $rolesHtml .= '<span class="badge badge-info badge-round badge-sm me-1 mb-1">' . e($roleName) . '</span>';
                }
                $row['role'] = '<div class="userDatatable-content">' . $rolesHtml . '</div>';
            } else {
                $row['role'] = '<span class="color-gray">-</span>';
            }

            // Active Status
            $row['active'] = $user->active ?? true;
            
            // Block Status
            $row['block'] = $user->block ?? false;

            // Image
            $row['image'] = $user->image;

            // Created At
            $row['created_at'] = $user->created_at ? $user->created_at : '-';

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
