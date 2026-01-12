<?php

namespace Modules\Order\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Order\app\Http\Requests\ShippingRequest;
use Modules\Order\app\Http\Resources\ShippingResource;
use Modules\Order\app\Services\ShippingService;
use Modules\SystemSetting\app\Models\SiteInformation;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function __construct(
        protected ShippingService $shippingService,
        protected \Modules\AreaSettings\app\Services\CityService $cityService,
        protected \Modules\CategoryManagment\app\Services\CategoryService $categoryService
    )
    {
        $this->middleware('can:shippings.index')->only(['index', 'datatable']);
        $this->middleware('can:shippings.create')->only(['create', 'store']);
        $this->middleware('can:shippings.show')->only(['show']);
        $this->middleware('can:shippings.edit')->only(['edit', 'update']);
        $this->middleware('can:shippings.change-status')->only(['changeStatus']);
        $this->middleware('can:shippings.delete')->only(['destroy']);
    }

    /**
     * Display a listing of shippings (datatable)
     */
    public function index($lang, $countryCode)
    {
        $shippingSettings = SiteInformation::first();
        return view('order::shippings.index', compact('shippingSettings'));
    }

    /**
     * Update shipping settings
     */
    public function updateSettings(Request $request)
    {
        try {
            $siteInfo = SiteInformation::first();
            
            if (!$siteInfo) {
                return response()->json([
                    'success' => false,
                    'message' => trans('shipping.error_saving_settings')
                ], 500);
            }

            // Check if any setting is being changed
            $oldDepartments = $siteInfo->shipping_allow_departments;
            $oldCategories = $siteInfo->shipping_allow_categories;
            $oldSubCategories = $siteInfo->shipping_allow_sub_categories;

            $newDepartments = $request->boolean('shipping_allow_departments');
            $newCategories = $request->boolean('shipping_allow_categories');
            $newSubCategories = $request->boolean('shipping_allow_sub_categories');

            // If any setting changed, delete all shipping methods
            if ($oldDepartments != $newDepartments || $oldCategories != $newCategories || $oldSubCategories != $newSubCategories) {
                // Delete all shippings (this will cascade delete pivot table entries)
                \Modules\Order\app\Models\Shipping::query()->forceDelete();
            }

            $siteInfo->update([
                'shipping_allow_departments' => $newDepartments,
                'shipping_allow_categories' => $newCategories,
                'shipping_allow_sub_categories' => $newSubCategories,
            ]);

            return response()->json([
                'success' => true,
                'message' => trans('shipping.settings_saved_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('shipping.error_saving_settings')
            ], 500);
        }
    }

    /**
     * Datatable endpoint for server-side processing
     */
    public function datatable($lang, $countryCode, Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'active' => $request->input('active'),
            'created_date_from' => $request->input('created_date_from'),
            'created_date_to' => $request->input('created_date_to'),
        ];

        $shippings = $this->shippingService->getAllShippings($filters);

        // Format data with index for DataTables using Resource
        $data = [];
        $startIndex = ($shippings->currentPage() - 1) * $shippings->perPage() + 1;
        foreach ($shippings->items() as $index => $shipping) {
            $shippingData = ShippingResource::make($shipping)->toArray($request);
            $shippingData['index'] = $startIndex + $index;
            $data[] = $shippingData;
        }

        return response()->json([
            'draw' => $request->get('draw', 1),
            'recordsTotal' => $shippings->total(),
            'recordsFiltered' => $shippings->total(),
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new shipping
     */
    public function create($lang, $countryCode)
    {
        $languages = \App\Models\Language::all();

        // Get country ID from country code in URL or session
        $country = \Modules\AreaSettings\app\Models\Country::where('code', strtoupper($countryCode))->first();
        if (!$country) {
            abort(404, 'Country not found');
        }

        $cities = $this->cityService->getCitiesByCountry($country->id);
        
        // Get shipping settings
        $shippingSettings = SiteInformation::first();
        
        // Get data based on settings
        $departments = collect();
        $categories = collect();
        $subCategories = collect();
        
        if ($shippingSettings?->shipping_allow_departments) {
            $departments = \Modules\CategoryManagment\app\Models\Department::active()->get();
        }
        
        if ($shippingSettings?->shipping_allow_categories) {
            $categories = $this->categoryService->getActiveCategories();
        }
        
        if ($shippingSettings?->shipping_allow_sub_categories) {
            $subCategories = \Modules\CategoryManagment\app\Models\SubCategory::active()->get();
        }

        return view('order::shippings.form', compact('languages', 'cities', 'categories', 'departments', 'subCategories', 'shippingSettings'));
    }

    /**
     * Store a newly created shipping in storage
     */
    public function store($lang, $countryCode, ShippingRequest $request)
    {
        try {
            $shipping = $this->shippingService->createShipping($request->validated());

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('shipping.created_successfully'),
                    'redirect' => route('admin.shippings.index'),
                    'data' => $shipping
                ], 201);
            }

            return redirect()->route('admin.shippings.index')
                           ->with('success', trans('shipping.created_successfully'));
        } catch (\Exception $e) {
            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('shipping.error_creating'),
                    'errors' => []
                ], 500);
            }

            return back()->with('error', trans('shipping.error_creating'));
        }
    }

    /**
     * Display the specified shipping
     */
    public function show($lang, $countryCode, $id)
    {
        $shipping = $this->shippingService->getShippingById($id);
        $shippingSettings = SiteInformation::first();
        return view('order::shippings.show', compact('shipping', 'shippingSettings'));
    }

    /**
     * Show the form for editing the specified shipping
     */
    public function edit($lang, $countryCode, $id)
    {
        $shipping = $this->shippingService->getShippingById($id);
        $languages = \App\Models\Language::all();

        // Get country ID from country code in URL
        $country = \Modules\AreaSettings\app\Models\Country::where('code', strtoupper($countryCode))->first();
        if (!$country) {
            abort(404, 'Country not found');
        }

        $cities = $this->cityService->getCitiesByCountry($country->id);
        
        // Get shipping settings
        $shippingSettings = SiteInformation::first();
        
        // Get data based on settings
        $departments = collect();
        $categories = collect();
        $subCategories = collect();
        
        if ($shippingSettings?->shipping_allow_departments) {
            $departments = \Modules\CategoryManagment\app\Models\Department::active()->get();
        }
        
        if ($shippingSettings?->shipping_allow_categories) {
            $categories = $this->categoryService->getActiveCategories();
        }
        
        if ($shippingSettings?->shipping_allow_sub_categories) {
            $subCategories = \Modules\CategoryManagment\app\Models\SubCategory::active()->get();
        }

        return view('order::shippings.form', compact('shipping', 'languages', 'cities', 'categories', 'departments', 'subCategories', 'shippingSettings'));
    }

    /**
     * Update the specified shipping in storage
     */
    public function update($lang, $countryCode, $id, ShippingRequest $request)
    {
        try {
            $shipping = $this->shippingService->updateShipping($id, $request->validated());

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('shipping.updated_successfully'),
                    'redirect' => route('admin.shippings.index'),
                    'data' => $shipping
                ], 200);
            }

            return redirect()->route('admin.shippings.index')
                           ->with('success', trans('shipping.updated_successfully'));
        } catch (\Exception $e) {
            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('shipping.error_updating'),
                    'errors' => []
                ], 500);
            }

            return back()->with('error', trans('shipping.error_updating'));
        }
    }

    /**
     * Remove the specified shipping from storage
     */
    public function destroy($lang, $countryCode, $id, Request $request)
    {
        try {
            $this->shippingService->deleteShipping($id);
            
            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'status' => true,
                    'message' => trans('shipping.deleted_successfully')
                ]);
            }
            
            return redirect()->route('admin.shippings.index')
                           ->with('success', trans('shipping.deleted_successfully'));
        } catch (\Exception $e) {
            \Log::error('Error deleting shipping: ' . $e->getMessage());
            
            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'status' => false,
                    'message' => trans('shipping.error_deleting')
                ], 500);
            }
            
            return back()->with('error', trans('shipping.error_deleting'));
        }
    }

    /**
     * Change shipping status
     */
    public function changeStatus($lang, $countryCode, $id, Request $request)
    {
        try {
            $status = $request->input('status');
            $this->shippingService->changeStatus($id, $status);
            return response()->json([
                'status' => true,
                'message' => trans('shipping.status_changed_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => trans('shipping.error_changing_status')
            ], 500);
        }
    }
}
