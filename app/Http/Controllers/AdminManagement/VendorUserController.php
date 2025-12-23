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
        return view('pages.vendor_users_management.vendor_user.index', compact('languages'));
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
        $vendors = $this->vendorService->getAllVendors();
        
        // Get current country_id for filtering
        $countryCode = request()->route('countryCode') ?? session('country_code');
        $countryCode = strtoupper($countryCode);
        $currentCountryId = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id');
        
        // Get roles filtered by vendor_id for vendor users
        $rolesQuery = $this->roleService->getRolesQuery(['type' => 'vendor_user']);
        
        // Filter by country: show roles for current country OR system roles (null country_id)
        if ($currentCountryId) {
            $rolesQuery->where(function($q) use ($currentCountryId) {
                $q->where('country_id', $currentCountryId)
                  ->orWhereNull('country_id');
            });
        }
        
        // If user is a vendor, filter roles by their vendor_id or system roles (null vendor_id)
        if (auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor) {
                $rolesQuery->where(function($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id)
                      ->orWhereNull('vendor_id');
                });
            }
            $roles = $rolesQuery->get();
        } else {
            // For admins, start with empty roles - they will be loaded via AJAX when vendor is selected
            $roles = collect([]);
        }
        
        return view('pages.vendor_users_management.vendor_user.form', compact('languages', 'vendors', 'roles'));
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
                    'redirect' => route('admin.vendor-users-management.vendor-users.index')
                ]);
            }

            return redirect()->route('admin.vendor-users-management.vendor-users.index')
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
    public function show($lang, $countryCode, string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $user = $this->vendorUserService->getVendorUserById((int) $id);
            return view('pages.vendor_users_management.vendor_user.view', compact('user', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.vendor-users-management.vendor-users.index')
                ->with('error', __('admin.vendor_user_not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($lang, $countryCode, string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $user = $this->vendorUserService->getVendorUserById((int) $id);
            $vendors = $this->vendorService->getAllVendors();
            
            // Get current country_id for filtering
            $urlCountryCode = request()->route('countryCode') ?? session('country_code');
            $urlCountryCode = strtoupper($urlCountryCode);
            $currentCountryId = \Modules\AreaSettings\app\Models\Country::where('code', $urlCountryCode)->value('id');
            
            // Get roles filtered by vendor_id for vendor users
            $rolesQuery = $this->roleService->getRolesQuery(['type' => 'vendor_user']);
            
            // Filter by country: show roles for current country OR system roles (null country_id)
            if ($currentCountryId) {
                $rolesQuery->where(function($q) use ($currentCountryId) {
                    $q->where('country_id', $currentCountryId)
                      ->orWhereNull('country_id');
                });
            }
            
            // If user is a vendor, filter roles by their vendor_id or system roles (null vendor_id)
            if (auth()->user()->isVendor()) {
                $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                if ($vendor) {
                    $rolesQuery->where(function($q) use ($vendor) {
                        $q->where('vendor_id', $vendor->id)
                          ->orWhereNull('vendor_id');
                    });
                }
            } else {
                // For admins editing a user, filter by the user's vendor_id
                if ($user->vendor_id) {
                    $rolesQuery->where(function($q) use ($user) {
                        $q->where('vendor_id', $user->vendor_id)
                          ->orWhereNull('vendor_id');
                    });
                }
            }
            
            $roles = $rolesQuery->get();
            
            return view('pages.vendor_users_management.vendor_user.form', compact('user', 'languages', 'vendors', 'roles'));
        } catch (\Exception $e) {
            return redirect()->route('admin.vendor-users-management.vendor-users.index')
                ->with('error', __('admin.vendor_user_not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VendorUserRequest $request, $lang, $countryCode, string $id)
    {
        $validated = $request->validated();

        try {
            $this->vendorUserService->updateVendorUser((int) $id, $validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('admin.vendor_user_updated_successfully'),
                    'redirect' => route('admin.vendor-users-management.vendor-users.index')
                ]);
            }

            return redirect()->route('admin.vendor-users-management.vendor-users.index')
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
    public function destroy(Request $request, $lang, $countryCode, string $id)
    {
        try {
            $this->vendorUserService->deleteVendorUser((int) $id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('admin.vendor_user_deleted_successfully'),
                    'redirect' => route('admin.vendor-users-management.vendor-users.index')
                ]);
            }

            return redirect()->route('admin.vendor-users-management.vendor-users.index')
                ->with('success', __('admin.vendor_user_deleted_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin.error_deleting_vendor_user') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.vendor-users-management.vendor-users.index')
                ->with('error', __('admin.error_deleting_vendor_user') . ': ' . $e->getMessage());
        }
    }

    /**
     * Change vendor user status.
     */
    public function changeStatus(Request $request, $lang, $countryCode, string $id)
    {
        try {
            $this->vendorUserService->changeStatus((int) $id, $request->status, $request->type);
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
