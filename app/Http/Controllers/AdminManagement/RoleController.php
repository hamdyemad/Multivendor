<?php

namespace App\Http\Controllers\AdminManagement;

use App\Actions\RoleAction;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Language;
use App\Models\Permession;
use App\Services\LanguageService;
use App\Services\RoleService;
use App\Traits\Res;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use Res;

    public function __construct(
        protected RoleService $roleService,
        protected RoleAction $roleAction,
        protected LanguageService $languageService)
    {
        // Admin roles middleware
        $this->middleware('can:roles.index')->only(['index', 'datatable']);
        $this->middleware('can:roles.show')->only(['show']);
        $this->middleware('can:roles.create')->only(['create', 'store']);
        $this->middleware('can:roles.edit')->only(['edit', 'update']);
        $this->middleware('can:roles.delete')->only(['destroy']);

        // Vendor user roles middleware
        $this->middleware('can:roles.index')->only(['vendorUserRolesIndex', 'vendorUserRolesDatatable']);
        $this->middleware('can:roles.show')->only(['vendorUserRolesShow']);
        $this->middleware('can:roles.create')->only(['vendorUserRolesCreate', 'vendorUserRolesStore']);
        $this->middleware('can:roles.edit')->only(['vendorUserRolesEdit', 'vendorUserRolesUpdate']);
        $this->middleware('can:roles.delete')->only(['vendorUserRolesDestroy']);
    }

    /**
     * Display a listing of the roles.
     */
    public function index(Request $request)
    {
        $languages = $this->languageService->getAll();
        $data = [
            'languages' => $languages,
            'title' => trans('menu.admin managment.roles managment'),
        ];
        return view('pages.admin_management.roles.index', $data);
    }

    /**
     * Get roles data for DataTables AJAX
     */
    public function datatable(Request $request)
    {
        $data = [
            'page' => $request->get('page', 1),
            'draw' => $request->get('draw', 1),
            'start' => $request->get('start', 0),
            'length' => $request->get('length', 10),
            'orderColumnIndex' => $request->get('order')[0]['column'] ?? 0,
            'orderDirection' => $request->get('order')[0]['dir'] ?? 'desc',
            'search' => $request->get('search'),
            'created_date_from' => $request->get('created_date_from'),
            'created_date_to' => $request->get('created_date_to'),
        ];

        // $response = $this->roleService->getDataTable($data);
        $response = $this->roleAction->getDataTable($data);
        return response()->json([
            'data' => $response['data'],
            'recordsTotal' => $response['totalRecords'],
            'recordsFiltered' => $response['filteredRecords'],
            'current_page' => $response['dataPaginated']->currentPage(),
            'last_page' => $response['dataPaginated']->lastPage(),
            'per_page' => $response['dataPaginated']->perPage(),
            'total' => $response['dataPaginated']->total(),
            'from' => $response['dataPaginated']->firstItem(),
            'to' => $response['dataPaginated']->lastItem()
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $languages = $this->languageService->getAll();
        $type = request('type');
        $groupedPermissions = $this->roleService->getGroupedPermissions($type);
        // return auth()->user()->roles;
        // return $groupedPermissions;
        $data = [
            'languages' => $languages,
            'groupedPermissions' => $groupedPermissions,
            'title' => __('roles.create_role'),
            'type' => $type, // Pass type to view
        ];
        return view('pages.admin_management.roles.form', $data);
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        // Create the role
        $this->roleService->createRole($request->validated());

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Role created successfully'),
                'redirect' => route('admin.admin-management.roles.index')
            ]);
        }

        return redirect()->route('admin.admin-management.roles.index')
                        ->with('success', __('Role created successfully'));
    }

    /**
     * Display the specified role.
     */
    public function show($lang, $countryCode, Role $role)
    {
        $languages = $this->languageService->getAll();
        $role = $this->roleService->getRoleById($role->id);
        $data = [
            'role' => $role,
            'languages' => $languages,
            'title' => __('roles.show_role'),
        ];
        return view('pages.admin_management.roles.show', $data);
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit($lang, $countryCode, Role $role)
    {
        if ($role->is_system_protected) {
            return redirect()->route('admin.admin-management.roles.index')
                ->with('error', __('This role is system protected and cannot be edited.'));
        }
        $role = $this->roleService->getRoleById($role->id);
        $languages = $this->languageService->getAll();
        $groupedPermissions = $this->roleService->getGroupedPermissions();
        $data = [
            'role' => $role,
            'languages' => $languages,
            'groupedPermissions' => $groupedPermissions,
            'title' => __('roles.edit_role'),
        ];
        return view('pages.admin_management.roles.form', $data);
    }

    /**
     * Update the specified role in storage.
     */
    public function update(UpdateRoleRequest $request, $lang, $countryCode, Role $role)
    {
        if ($role->is_system_protected) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => __('This role is system protected and cannot be edited.')], 403);
            }
            return redirect()->route('admin.admin-management.roles.index')
                ->with('error', __('This role is system protected and cannot be edited.'));
        }
        // Update the role
        $this->roleService->updateRole($role, $request->validated());

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Role updated successfully'),
                'redirect' => route('admin.admin-management.roles.index')
            ]);
        }

        return redirect()->route('admin.admin-management.roles.index')
                        ->with('success', __('Role updated successfully'));
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Request $request, $lang, $countryCode, Role $role)
    {
        // Check if role is system protected
        if ($role->is_system_protected) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('This is a system role and cannot be deleted. You can only edit it.')
                ], 403);
            }

            return redirect()->route('admin.admin-management.roles.index')
                            ->with('error', __('This is a system role and cannot be deleted. You can only edit it.'));
        }

        $this->roleService->deleteRole($role);

        // Check if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Role deleted successfully')
            ]);
        }

        return redirect()->route('admin.admin-management.roles.index')
                        ->with('success', __('Role deleted successfully'));
    }

    // ==================== Vendor User Roles Methods ====================

    /**
     * Display a listing of vendor user roles.
     */
    public function vendorUserRolesIndex(Request $request)
    {
        $languages = $this->languageService->getAll();
        $data = [
            'languages' => $languages,
            'title' => trans('Vendor Users Roles Management'),
            'type' => 'vendor_user',
        ];
        return view('pages.vendor_users_management.roles.index', $data);
    }

    /**
     * Get vendor user roles data for DataTables AJAX
     */
    public function vendorUserRolesDatatable(Request $request)
    {
        $data = [
            'page' => $request->get('page', 1),
            'draw' => $request->get('draw', 1),
            'start' => $request->get('start', 0),
            'length' => $request->get('length', 10),
            'orderColumnIndex' => $request->get('order')[0]['column'] ?? 0,
            'orderDirection' => $request->get('order')[0]['dir'] ?? 'desc',
            'search' => $request->get('search'),
            'created_date_from' => $request->get('created_date_from'),
            'created_date_to' => $request->get('created_date_to'),
            'type' => 'vendor_user', // Filter for vendor user roles
        ];

        $response = $this->roleAction->getDataTable($data);
        return response()->json([
            'data' => $response['data'],
            'recordsTotal' => $response['totalRecords'],
            'recordsFiltered' => $response['filteredRecords'],
            'current_page' => $response['dataPaginated']->currentPage(),
            'last_page' => $response['dataPaginated']->lastPage(),
            'per_page' => $response['dataPaginated']->perPage(),
            'total' => $response['dataPaginated']->total(),
            'from' => $response['dataPaginated']->firstItem(),
            'to' => $response['dataPaginated']->lastItem()
        ]);
    }

    /**
     * Show the form for creating a new vendor user role.
     */
    public function vendorUserRolesCreate()
    {
        $languages = $this->languageService->getAll();
        $groupedPermissions = $this->roleService->getGroupedPermissions('vendor_user');
        $data = [
            'languages' => $languages,
            'groupedPermissions' => $groupedPermissions,
            'title' => __('roles.create_role'),
            'type' => 'vendor_user',
        ];
        return view('pages.vendor_users_management.roles.form', $data);
    }

    /**
     * Store a newly created vendor user role in storage.
     */
    public function vendorUserRolesStore(StoreRoleRequest $request)
    {
        $this->roleService->createRole($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Role created successfully'),
                'redirect' => route('admin.vendor-users-management.roles.index')
            ]);
        }

        return redirect()->route('admin.vendor-users-management.roles.index')
                        ->with('success', __('Role created successfully'));
    }

    /**
     * Display the specified vendor user role.
     */
    public function vendorUserRolesShow($lang, $countryCode, Role $role)
    {
        $languages = $this->languageService->getAll();
        $role = $this->roleService->getRoleById($role->id);
        $data = [
            'role' => $role,
            'languages' => $languages,
            'title' => __('roles.show_role'),
        ];
        return view('pages.vendor_users_management.roles.show', $data);
    }

    /**
     * Show the form for editing the specified vendor user role.
     */
    public function vendorUserRolesEdit($lang, $countryCode, Role $role)
    {
        if ($role->is_system_protected) {
            return redirect()->route('admin.vendor-users-management.roles.index')
                ->with('error', __('This role is system protected and cannot be edited.'));
        }
        $role = $this->roleService->getRoleById($role->id);
        $languages = $this->languageService->getAll();
        $groupedPermissions = $this->roleService->getGroupedPermissions('vendor_user');
        $data = [
            'role' => $role,
            'languages' => $languages,
            'groupedPermissions' => $groupedPermissions,
            'title' => __('roles.edit_role'),
            'type' => 'vendor_user',
        ];
        return view('pages.vendor_users_management.roles.form', $data);
    }

    /**
     * Update the specified vendor user role in storage.
     */
    public function vendorUserRolesUpdate(UpdateRoleRequest $request, $lang, $countryCode, Role $role)
    {
        if ($role->is_system_protected) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => __('This role is system protected and cannot be edited.')], 403);
            }
            return redirect()->route('admin.vendor-users-management.roles.index')
                ->with('error', __('This role is system protected and cannot be edited.'));
        }
        
        $this->roleService->updateRole($role, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Role updated successfully'),
                'redirect' => route('admin.vendor-users-management.roles.index')
            ]);
        }

        return redirect()->route('admin.vendor-users-management.roles.index')
                        ->with('success', __('Role updated successfully'));
    }

    /**
     * Remove the specified vendor user role from storage.
     */
    public function vendorUserRolesDestroy(Request $request, $lang, $countryCode, Role $role)
    {
        if ($role->is_system_protected) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('This is a system role and cannot be deleted. You can only edit it.')
                ], 403);
            }

            return redirect()->route('admin.vendor-users-management.roles.index')
                            ->with('error', __('This is a system role and cannot be deleted. You can only edit it.'));
        }

        $this->roleService->deleteRole($role);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Role deleted successfully')
            ]);
        }

        return redirect()->route('admin.vendor-users-management.roles.index')
                        ->with('success', __('Role deleted successfully'));
    }
}
