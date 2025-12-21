<?php

namespace App\Http\Controllers\AdminManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminManagement\VendorUserRequest;
use App\Services\VendorUserService;
use App\Services\LanguageService;
use App\Services\RoleService;
use Modules\Vendor\app\Services\VendorService;
use App\Actions\VendorUserAction;
use Illuminate\Http\Request;

class VendorUserController extends Controller
{
    public function __construct(
        protected VendorUserService $vendorUserService,
        protected LanguageService $languageService,
        protected RoleService $roleService,
        protected VendorService $vendorService,
        protected VendorUserAction $vendorUserAction
    ) {
        $this->middleware('can:vendor-users.index')->only(['index', 'datatable']);
        $this->middleware('can:vendor-users.create')->only(['create', 'store']);
        $this->middleware('can:vendor-users.edit')->only(['edit', 'update']);
        $this->middleware('can:vendor-users.delete')->only(['destroy']);
        $this->middleware('can:vendor-users.show')->only(['show']);
        $this->middleware('can:vendor-users.change-status')->only(['changeStatus']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $languages = $this->languageService->getAll();
        return view('pages.admin_management.vendor_user.index', compact('languages'));
    }

    /**
     * Get vendor users data for DataTables AJAX
     */
    public function datatable(Request $request)
    {
        return $this->vendorUserAction->datatable($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getAll();
        $roles = $this->roleService->getAllRoles();
        $vendors = $this->vendorService->getAllVendors();
        return view('pages.admin_management.vendor_user.form', compact('languages', 'roles', 'vendors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VendorUserRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->vendorUserService->createVendorUser($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('admin.vendor_user_created_successfully'),
                    'redirect' => route('admin.admin-management.vendor-users.index')
                ]);
            }

            return redirect()->route('admin.admin-management.vendor-users.index')
                ->with('success', __('admin.vendor_user_created_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin.error_creating_vendor_user') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('admin.error_creating_vendor_user') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $user = $this->vendorUserService->getVendorUserById($id);
            return view('pages.admin_management.vendor_user.view', compact('user', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.admin-management.vendor-users.index')
                ->with('error', __('admin.vendor_user_not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $roles = $this->roleService->getAllRoles();
            $vendors = $this->vendorService->getAllVendors();
            $user = $this->vendorUserService->getVendorUserById($id);
            return view('pages.admin_management.vendor_user.form', compact('user', 'languages', 'roles', 'vendors'));
        } catch (\Exception $e) {
            return redirect()->route('admin.admin-management.vendor-users.index')
                ->with('error', __('admin.vendor_user_not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VendorUserRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $this->vendorUserService->updateVendorUser($id, $validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('admin.vendor_user_updated_successfully'),
                    'redirect' => route('admin.admin-management.vendor-users.index')
                ]);
            }

            return redirect()->route('admin.admin-management.vendor-users.index')
                ->with('success', __('admin.vendor_user_updated_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin.error_updating_vendor_user') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('admin.error_updating_vendor_user') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $this->vendorUserService->deleteVendorUser($id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('admin.vendor_user_deleted_successfully'),
                    'redirect' => route('admin.admin-management.vendor-users.index')
                ]);
            }

            return redirect()->route('admin.admin-management.vendor-users.index')
                ->with('success', __('admin.vendor_user_deleted_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin.error_deleting_vendor_user') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.admin-management.vendor-users.index')
                ->with('error', __('admin.error_deleting_vendor_user') . ': ' . $e->getMessage());
        }
    }

    /**
     * Change vendor user status.
     */
    public function changeStatus(Request $request, string $id)
    {
        try {
            $this->vendorUserService->changeStatus($id, $request->status, $request->type);
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
