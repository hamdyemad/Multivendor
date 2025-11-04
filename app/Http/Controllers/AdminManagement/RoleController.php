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
        $this->middleware('can:roles.index')->only(['index']);
        $this->middleware('can:roles.show')->only(['show']);
        $this->middleware('can:roles.create')->only(['create', 'store']);
        $this->middleware('can:roles.edit')->only(['edit', 'update']);
        $this->middleware('can:roles.delete')->only(['destroy']);
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
        $groupedPermissions = $this->roleService->getGroupedPermissions();
        $data = [
            'languages' => $languages,
            'groupedPermissions' => $groupedPermissions,
            'title' => __('roles.create_role'),
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
    public function show(Role $role)
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
    public function edit(Role $role)
    {
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
    public function update(UpdateRoleRequest $request, Role $role)
    {
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
    public function destroy(Request $request, Role $role)
    {
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
}
