<?php

namespace Modules\Vendor\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Vendor\app\Services\VendorService;
use App\Services\LanguageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Modules\AreaSettings\app\Resources\CountryResource;
use Modules\AreaSettings\app\Services\CountryService;
use Modules\CategoryManagment\app\Http\Resources\ActivityResource;
use Modules\CategoryManagment\app\Services\ActivityService;
use Modules\Vendor\app\Actions\VendorAction;
use Modules\Vendor\app\Http\Requests\Vendor\VendorRequest;
use Modules\Vendor\app\Models\Vendor;

class VendorController extends Controller {

    public function __construct(
        protected VendorService $vendorService,
        protected VendorAction $vendorAction,
        protected CountryService $countryService,
        protected ActivityService $activityService,
        protected LanguageService $languageService,
    ) {}

    public function index() {
        $languages = $this->languageService->getAll();
        // Get vendor statistics
        $statistics = \Modules\Vendor\app\Models\Vendor::getVendorsStatistics();

        $data = [
            'title' => 'Vendors Management',
            'languages' => $languages,
            'statistics' => $statistics
        ];
        return view('vendor::vendors.index', $data);
    }

    public function datatable(Request $request, $lang, $countryCode)
    {
        $data = [
            'page' => $request->get('page', 1),
            'draw' => $request->get('draw', 1),
            'start' => $request->get('start', 0),
            'length' => $request->get('length', 10),
            'orderColumnIndex' => $request->get('order')[0]['column'] ?? 0,
            'orderDirection' => $request->get('order')[0]['dir'] ?? 'desc',
            'search' => $request->get('search'),
            'active' => $request->get('active'),
            'created_date_from' => $request->get('created_date_from'),
            'created_date_to' => $request->get('created_date_to'),
        ];

        $response = $this->vendorAction->getDataTable($data);
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
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }


    public function create($lang, $countryCode) {
        // Get all countries and activities for select dropdowns
        $countriesData = $this->countryService->getAllCountries([], 1000);
        $activitiesData = $this->activityService->getAllActivities(0, []);
        // Extract items from paginated results
        $countries = CountryResource::collection($countriesData)->resolve();
        // Pass activities as collection for form (need getTranslation method)
        $activities = $activitiesData;

        // Get languages for translations
        $languages = $this->languageService->getAll();

        $data = [
            'title' => __('vendor::vendor.add_vendor'),
            'countries' => $countries,
            'activities' => $activities,
            'languages' => $languages
        ];
        return view('vendor::vendors.form', $data);
    }

    public function store($lang, $countryCode, VendorRequest $request)
    {
        try {
            $data = $request->validated();
            $vendor = $this->vendorService->createVendor($data);
            // Check if it's an AJAX request
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('vendor::vendor.vendor_created_successfully'),
                    'redirect' => route('admin.vendors.index'),
                    'vendor' => $vendor
                ]);
            }

            return redirect()
                ->route('admin.vendors.index')
                ->with('success', __('vendor::vendor.vendor_created_successfully'));
        } catch (Exception $e) {
            Log::error("Vendor creation failed", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Check if it's an AJAX request
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('vendor::vendor.error_creating_vendor'),
                    'error_details' => $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('vendor::vendor.error_creating_vendor'))
                ->with('error_details', $e->getMessage());
        }
    }

    public function show($lang, $countryCode, $id) {
        $vendor = $this->vendorService->getVendorById($id);
        $languages = $this->languageService->getAll();
        $data = [
            'title' => __('vendor::vendor.vendor_details'),
            'vendor' => $vendor,
            'languages' => $languages
        ];
        return view('vendor::vendors.show', $data);
    }

    public function edit($lang, $countryCode, $id) {
        $vendor = $this->vendorService->getVendorById($id);
        // Get all countries and activities for select dropdowns
        $countriesData = $this->countryService->getAllCountries([], 1000);
        $activitiesData = $this->activityService->getAllActivities(0, []);

        // Extract items from paginated results
        $countries = CountryResource::collection($countriesData)->resolve();
        // Pass activities as collection for form (need getTranslation method)
        $activities = $activitiesData;
        // Get languages for translations
        $languages = $this->languageService->getAll();
        $data = [
            'title' => __('vendor::vendor.edit_vendor'),
            'vendor' => $vendor,
            'countries' => $countries,
            'activities' => $activities,
            'languages' => $languages
        ];
        return view('vendor::vendors.form', $data);
    }

    public function update($lang, $countryCode, VendorRequest $request, $id) {
        try {
            $this->vendorService->updateVendor($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => __('vendor::vendor.vendor_updated_successfully'),
                'redirect' => route('admin.vendors.index')
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Vendor update failed', [
                'vendor_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('vendor::vendor.error_updating_vendor'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($lang, $countryCode, $id) {
        try {
            $this->vendorService->deleteVendor($id);

            return response()->json([
                'success' => true,
                'message' => __('vendor::vendor.vendor_deleted_successfully')
            ]);
        } catch (Exception $e) {
            Log::error('Vendor deletion failed', [
                'vendor_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('vendor::vendor.error_deleting_vendor'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change vendor active status
     */
    public function changeStatus($lang, $countryCode, Request $request, $id)
    {
        try {
            $vendor = $this->vendorService->getVendorById($id);

            if (!$vendor) {
                return response()->json([
                    'success' => false,
                    'message' => __('vendor::vendor.vendor_not_found')
                ], 404);
            }

            $newStatus = !$vendor->active;
            $vendor->update(['active' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => __('vendor::vendor.status_changed_successfully'),
                'new_status' => $newStatus,
                'status_text' => $newStatus ? __('vendor::vendor.active') : __('vendor::vendor.inactive')
            ]);
        } catch (Exception $e) {
            Log::error('Vendor status change failed', [
                'vendor_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('vendor::vendor.error_changing_status')
            ], 500);
        }
    }

    /**
     * Delete a vendor document
     */
    public function destroyDocument($lang, $countryCode, $vendorId, $documentId)
    {
        try {
            $vendor = $this->vendorService->getVendorById($vendorId);
            $document = $vendor->documents()->findOrFail($documentId);

            // Delete the file from storage if it exists
            if ($document->path && \Storage::disk('public')->exists($document->path)) {
                \Storage::disk('public')->delete($document->path);
            }

            // Delete document translations
            $document->translations()->delete();

            // Delete the document record
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => trans('vendor::vendor.document_deleted_successfully') ?? 'Document deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Document deletion failed', [
                'vendor_id' => $vendorId,
                'document_id' => $documentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => trans('vendor::vendor.error_deleting_document') ?? 'Error deleting document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
