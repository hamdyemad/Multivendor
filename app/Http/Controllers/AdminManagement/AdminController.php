<?php

namespace App\Http\Controllers\AdminManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminManagement\AdminRequest;
use App\Services\AdminService;
use App\Services\LanguageService;
use App\Services\RoleService;
use App\Actions\AdminAction;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        protected AdminService $adminService,
        protected LanguageService $languageService,
        protected RoleService $roleService,
        protected AdminAction $adminAction
    ) {
        $this->middleware('can:admins.index')->only(['index', 'datatable']);
        $this->middleware('can:admins.create')->only(['create', 'store']);
        $this->middleware('can:admins.edit')->only(['edit', 'update']);
        $this->middleware('can:admins.delete')->only(['destroy']);
        $this->middleware('can:admins.show')->only(['show']);
        $this->middleware('can:admins.change-status')->only(['changeStatus']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $lang, $countryCode)
    {
        $languages = $this->languageService->getAll();
        return view('pages.admin_management.admin.index', compact('languages'));
    }

    /**
     * Get admins data for DataTables AJAX
     */
    public function datatable(Request $request, $lang, $countryCode)
    {
        return $this->adminAction->datatable($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($lang, $countryCode)
    {
        $languages = $this->languageService->getAll();
        $roles = $this->roleService->getAllRoles([
            'type' => 'admin'
        ], 0);
        return view('pages.admin_management.admin.form', compact('languages', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $request, $lang, $countryCode)
    {
        $validated = $request->validated();

        try {
            $this->adminService->createAdmin($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('admin.admin_created_successfully'),
                    'redirect' => route('admin.admin-management.admins.index')
                ]);
            }

            return redirect()->route('admin.admin-management.admins.index')
                ->with('success', __('admin.admin_created_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin.error_creating_admin') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('admin.error_creating_admin') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($lang, $countryCode, string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $admin = $this->adminService->getAdminById((int) $id);
            return view('pages.admin_management.admin.view', compact('admin', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.admin-management.admins.index')
                ->with('error', __('admin.admin_not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($lang, $countryCode, string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $admin = $this->adminService->getAdminById((int) $id);
            $roles = $this->roleService->getAllRoles([
                'type' => 'admin'
            ], 0);
            return view('pages.admin_management.admin.form', compact('admin', 'languages', 'roles'));
        } catch (\Exception $e) {
            return redirect()->route('admin.admin-management.admins.index')
                ->with('error', __('admin.admin_not_found'));
        }
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(AdminRequest $request, $lang, $countryCode, string $id)
    {
        $validated = $request->validated();

        try {
            $this->adminService->updateAdmin((int) $id, $validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('admin.admin_updated_successfully'),
                    'redirect' => route('admin.admin-management.admins.index')
                ]);
            }

            return redirect()->route('admin.admin-management.admins.index')
                ->with('success', __('admin.admin_updated_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin.error_updating_admin') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('admin.error_updating_admin') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $lang, $countryCode, string $id)
    {
        try {
            $this->adminService->deleteAdmin((int) $id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('admin.admin_deleted_successfully'),
                    'redirect' => route('admin.admin-management.admins.index')
                ]);
            }

            return redirect()->route('admin.admin-management.admins.index')
                ->with('success', __('admin.admin_deleted_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin.error_deleting_admin') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.admin-management.admins.index')
                ->with('error', __('admin.error_deleting_admin') . ': ' . $e->getMessage());
        }
    }

    /**
     * Change admin status.
     */
    public function changeStatus(Request $request, $lang, $countryCode, string $id)
    {
        try {
            $this->adminService->changeStatus((int) $id, $request->status, $request->type);
            return response()->json([
                'success' => true,
                'message' => __('admin.status_changed_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
