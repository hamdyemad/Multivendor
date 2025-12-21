<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\CatalogManagement\app\Actions\BundleCategoryAction;
use Modules\CatalogManagement\app\Services\BundleCategoryService;
use Modules\CatalogManagement\app\Interfaces\BundleCategoryRepositoryInterface;
use Modules\CatalogManagement\app\Http\Requests\BundleCategoryRequest;
use App\Services\LanguageService;
use Modules\AreaSettings\app\Models\Country;
use Modules\CatalogManagement\app\Models\BundleCategory;

class BundleCategoryController extends Controller
{
    public function __construct(
        protected BundleCategoryAction $bundleCategoryAction,
        protected BundleCategoryService $bundleCategoryService,
        protected BundleCategoryRepositoryInterface $bundleCategoryRepository,
        protected LanguageService $languageService
    ) {
        $this->middleware('can:bundle-categories.index')->only(['index', 'datatable']);
        $this->middleware('can:bundle-categories.create')->only(['create', 'store']);
        $this->middleware('can:bundle-categories.edit')->only(['edit', 'update']);
        $this->middleware('can:bundle-categories.delete')->only(['destroy']);
        $this->middleware('can:bundle-categories.show')->only(['show']);
        $this->middleware('can:bundle-categories.toggle-status')->only(['toggleStatus']);
    }

    /**
     * Display a listing of bundle categories
     */
    public function index()
    {
        $languages = $this->languageService->getAll();
        $data = [
            'languages' => $languages,
        ];

        return view('catalogmanagement::bundle-categories.index', $data);
    }

    /**
     * Datatable endpoint for server-side processing
     */
    public function datatable(Request $request)
    {
        try {
            // Get datatable data from action
            $result = $this->bundleCategoryAction->getDataTable($request->all());

            $dataPaginated = $result['dataPaginated'];

            return response()->json([
                'draw' => intval($request->input('draw', 1)), // Required for DataTables pagination
                'data' => $result['data'],
                'recordsTotal' => $result['totalRecords'],
                'recordsFiltered' => $result['filteredRecords'],
                'current_page' => $dataPaginated->currentPage(),
                'last_page' => $dataPaginated->lastPage(),
                'per_page' => $dataPaginated->perPage(),
                'total' => $dataPaginated->total(),
                'from' => $dataPaginated->firstItem(),
                'to' => $dataPaginated->lastItem()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading bundle categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new bundle category
     */
    public function create($lang, $countryCode)
    {
        $languages = $this->languageService->getAll();

        $data = [
            'languages' => $languages,
        ];

        return view('catalogmanagement::bundle-categories.form', $data);
    }

    /**
     * Store a newly created bundle category
     */
    public function store($lang, $countryCode, BundleCategoryRequest $request)
    {
        try {
            $validated = $request->validated();

            $this->bundleCategoryService->createBundleCategory($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('catalogmanagement::bundle_category.bundle_category_created'),
                    'redirect' => route('admin.bundle-categories.index')
                ]);
            }

            return redirect()->route('admin.bundle-categories.index')
                ->with('success', trans('catalogmanagement::bundle_category.bundle_category_created'));
        } catch (\Exception $e) {
            \Log::error('Bundle Category Creation Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('catalogmanagement::bundle_category.error_creating_bundle_category'),
                    'error' => config('app.debug') ? $e->getMessage() : null,
                    'debug' => config('app.debug') ? [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString()
                    ] : null
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', trans('catalogmanagement::bundle_category.error_creating_bundle_category'));
        }
    }

    /**
     * Display the specified bundle category
     */
    public function show($lang, $countryCode, $id)
    {
        $bundleCategory = $this->bundleCategoryRepository->getBundleCategoryById($id);
        $languages = $this->languageService->getAll();

        $data = [
            'bundleCategory' => $bundleCategory,
            'languages' => $languages,
        ];

        return view('catalogmanagement::bundle-categories.show', $data);
    }

    /**
     * Show the form for editing the specified bundle category
     */
    public function edit($lang, $countryCode, $id)
    {
        $bundleCategory = $this->bundleCategoryRepository->getBundleCategoryById($id);
        $languages = $this->languageService->getAll();

        $data = [
            'bundleCategory' => $bundleCategory,
            'languages' => $languages,
        ];

        return view('catalogmanagement::bundle-categories.form', $data);
    }

    /**
     * Update the specified bundle category
     */
    public function update($lang, $countryCode, BundleCategoryRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            $this->bundleCategoryService->updateBundleCategory($id, $validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('catalogmanagement::bundle_category.bundle_category_updated'),
                    'redirect' => route('admin.bundle-categories.index')
                ]);
            }

            return redirect()->route('admin.bundle-categories.index')
                ->with('success', trans('catalogmanagement::bundle_category.bundle_category_updated'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error updating bundle category')
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('Error updating bundle category'));
        }
    }

    /**
     * Remove the specified bundle category
     */
    public function destroy($lang, $countryCode, Request $request, $id)
    {
        try {
            \Log::info('Destroying bundle category', ['id' => $id]);

            $this->bundleCategoryService->deleteBundleCategory($id);

            \Log::info('Bundle category destroyed successfully', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => trans('catalogmanagement::bundle_category.bundle_category_deleted')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error destroying bundle category', [
                'id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => trans('catalogmanagement::bundle_category.error_deleting_bundle_category'),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Toggle bundle category status
     */
    public function toggleStatus(Request $request, $lang, $countryCode, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('catalogmanagement::bundle_category.invalid_status')
                ], 422);
            }

            $bundleCategory = $this->bundleCategoryRepository->getBundleCategoryById($id);
            $newStatus = $request->input('status');

            // Check if status is actually changing
            if ($bundleCategory->active == $newStatus) {
                return response()->json([
                    'success' => false,
                    'message' => trans('catalogmanagement::bundle_category.status_already_set')
                ], 422);
            }

            $this->bundleCategoryService->toggleBundleCategoryStatus($id);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('catalogmanagement::bundle_category.status_changed_successfully'),
                    'new_status' => $newStatus,
                    'redirect' => route('admin.bundle-categories.index')
                ]);
            }

            return redirect()->route('admin.bundle-categories.index')
                ->with('success', trans('catalogmanagement::bundle_category.status_changed_successfully'));

        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('catalogmanagement::bundle_category.error_changing_status') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->with('error', trans('catalogmanagement::bundle_category.error_changing_status') . ': ' . $e->getMessage());
        }
    }

}
