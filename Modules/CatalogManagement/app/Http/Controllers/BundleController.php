<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CatalogManagement\app\Services\BundleService;
use Modules\CatalogManagement\app\Http\Requests\BundleRequest;
use Modules\CatalogManagement\app\Http\Resources\BundleResource;
use App\Services\LanguageService;
use Modules\CatalogManagement\app\Services\BundleCategoryService;
use Modules\Vendor\app\Services\VendorService;
use Illuminate\Http\Request;

class BundleController extends Controller
{

    public function __construct(
        protected BundleService $bundleService,
        protected BundleCategoryService $bundleCategoryService,
        protected VendorService $vendorService,
        protected LanguageService $languageService,
        )
    {
    }

    /**
     * Display a listing of bundles
     */
    public function index($lang, $countryCode)
    {
        $vendors = $this->vendorService->getAllVendors([], 0);
        $vendors = $vendors->map(function($vendor) {
            return [
                'id' => $vendor->id,
                'name' => $vendor->name,
            ];
        });
        return view('catalogmanagement::bundles.index', compact('vendors'));
    }

    /**
     * Get bundles datatable
     */
    public function datatable($lang, $countryCode)
    {
        $filters = request()->all();
        $perPage = $filters['per_page'] ?? 15;
        $bundles = $this->bundleService->getAllBundles($filters, $perPage);

        // Format bundles data for DataTable
        $data = $bundles->map(function($bundle) {
            return [
                'id' => $bundle->id,
                'bundle_information' => [
                    'image' => $bundle->main_image ? asset('storage/' . $bundle->main_image->path) : '',
                    'name_en' => $bundle->getTranslation('name', 'en') ?? '-',
                    'name_ar' => $bundle->getTranslation('name', 'ar') ?? '-',
                ],
                'sku' => $bundle->sku ?? '-',
                'vendor' => $bundle->vendor?->name,
                'is_active' => $bundle->is_active,
                'admin_approval' => $bundle->admin_approval,
                'approval_reason' => $bundle->approval_reason,
                'created_at' => $bundle->created_at
            ];
        });

        return response()->json([
            'draw' => $filters['draw'] ?? 0,
            'recordsTotal' => $bundles->total() ?? 0,
            'recordsFiltered' => $bundles->total() ?? 0,
            'data' => $data,
        ]);
    }


    /**
     * Show the form for creating a new bundle
     */
    public function create($lang, $countryCode)
    {
        $languages = $this->languageService->getAll();
        $categories = $this->bundleCategoryService->getActiveBundleCategories();

        // Check if user is admin or vendor
        $isAdmin = in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds());
        $vendors = [];
        $userVendorId = null;

        if ($isAdmin) {
            // Admin can create bundles for any vendor
            $vendors = $this->vendorService->getAllVendors();
        } else {
            // Vendor can only create bundles for their own vendor
            $userVendorId = auth()->user()->vendor->id ?? null;
        }

        return view('catalogmanagement::bundles.form', compact('languages', 'vendors', 'categories', 'isAdmin', 'userVendorId'));
    }

    /**
     * Store a newly created bundle in storage
     */
    public function store($lang, $countryCode, BundleRequest $request)
    {
        \Log::info($request->all());
        try {
            $validated = $request->validated();
            $bundle = $this->bundleService->createBundle($validated);

            return response()->json([
                'message' => 'Bundle created successfully',
                'redirect' => route('admin.bundles.show', $bundle->id),
            ]);
        } catch (\Exception $e) {
            \Log::error('Bundle creation error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error creating bundle: ' . $e->getMessage(),
                'errors' => [],
            ], 422);
        }
    }

    /**
     * Display the specified bundle
     */
    public function show($lang, $countryCode, $id)
    {
        $bundle = $this->bundleService->getBundleById($id);
        $languages = $this->languageService->getAll();

        return view('catalogmanagement::bundles.show', compact('bundle', 'languages'));
    }

    /**
     * Show the form for editing the specified bundle
     */
    public function edit($lang, $countryCode, $id)
    {
        $bundle = $this->bundleService->getBundleById($id);
        $languages = $this->languageService->getAll();
        $vendors = $this->vendorService->getAllVendors();
        $categories = $this->bundleCategoryService->getActiveBundleCategories();
        $bundleResource = (new BundleResource($bundle))->resolve();

        // Check if user is admin or vendor
        $isAdmin = in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds());
        $vendors = [];
        $userVendorId = null;

        if ($isAdmin) {
            // Admin can create bundles for any vendor
            $vendors = $this->vendorService->getAllVendors();
        } else {
            // Vendor can only create bundles for their own vendor
            $userVendorId = auth()->user()->vendor->id ?? null;
        }


        return view('catalogmanagement::bundles.form', compact('bundle', 'bundleResource', 'languages', 'vendors', 'categories', 'userVendorId', 'isAdmin'));

    }

    /**
     * Update the specified bundle in storage
     */
    public function update($lang, $countryCode, $id, BundleRequest $request)
    {
        try {
            $bundle = $this->bundleService->getBundleById($id);
            $validated = $request->validated();
            $bundle = $this->bundleService->updateBundle($bundle, $validated);

            return response()->json([
                'message' => 'Bundle updated successfully',
                'redirect' => route('admin.bundles.show', $bundle->id),
            ]);
        } catch (\Exception $e) {
            \Log::error('Bundle update error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error updating bundle: ' . $e->getMessage(),
                'errors' => [],
            ], 422);
        }
    }

    /**
     * Remove the specified bundle from storage
     */
    public function destroy($lang, $countryCode, $id)
    {
        try {
            $bundle = $this->bundleService->getBundleById($id);
            $this->bundleService->deleteBundle($bundle);

            return response()->json([
                'message' => 'Bundle deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting bundle: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleStatus($lang, $countryCode, $id)
    {
        try {
            $bundle = $this->bundleService->getBundleById($id);
            $bundle = $this->bundleService->toggleActive($bundle);

            return response()->json([
                'status' => true,
                'message' => trans('catalogmanagement::bundle.status_changed_successfully'),
                'is_active' => $bundle->is_active,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => trans('catalogmanagement::bundle.error_changing_status'),
            ], 422);
        }
    }

    /**
     * Change bundle approval status
     * 0 = pending, 1 = approved, 2 = rejected
     */
    public function changeApproval(Request $request, $lang, $countryCode, $id)
    {
        try {
            $bundle = $this->bundleService->getBundleById($id);

            $action = $request->input('action');
            $reason = $request->input('reason', null);

            if (!in_array($action, ['approve', 'reject'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid action',
                ], 422);
            }

            // Map action to approval status: approve = 1, reject = 2
            $approvalStatus = ($action === 'approve') ? 1 : 2;
            $bundle = $this->bundleService->changeApprovalStatus($bundle, $approvalStatus, $reason);

            $message = ($action === 'approve')
                ? trans('catalogmanagement::bundle.bundle_approved_successfully')
                : trans('catalogmanagement::bundle.bundle_rejected_successfully');

            return response()->json([
                'status' => true,
                'message' => $message,
                'admin_approval' => $bundle->admin_approval,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error changing approval status: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Remove a product from bundle
     */
    public function destroyProduct($lang, $countryCode, $bundle, $product)
    {
        try {
            $bundleModel = $this->bundleService->getBundleById($bundle);
            $bundleProduct = $bundleModel->bundleProducts()->findOrFail($product);
            $bundleProduct->delete();

            return response()->json([
                'status' => true,
                'message' => trans('catalogmanagement::bundle.product_deleted_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => trans('catalogmanagement::bundle.error_deleting_product'),
            ], 422);
        }
    }

}
