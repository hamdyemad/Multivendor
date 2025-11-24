<?php

namespace Modules\AreaSettings\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\AreaSettings\app\Actions\RegionAction;
use Modules\AreaSettings\app\Http\Requests\RegionRequest;
use Modules\AreaSettings\app\Services\RegionService;
use Modules\AreaSettings\app\Services\CityService;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Modules\AreaSettings\app\Resources\CityResource;

class RegionController extends Controller
{
    public function __construct(
        protected RegionService $regionService,
        protected CityService $cityService,
        protected LanguageService $languageService,
        protected RegionAction $regionAction
    ) {
        $this->middleware('can:area.region.index')->only(['index']);
        $this->middleware('can:area.region.show')->only(['show']);
        $this->middleware('can:area.region.create')->only(['create', 'store']);
        $this->middleware('can:area.region.edit')->only(['edit', 'update', 'changeStatus']);
        $this->middleware('can:area.region.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get languages for table headers
        $languages = $this->languageService->getAll();

        // Get all cities for filter dropdown
        $cities = CityResource::collection($this->cityService->getAllCities())->resolve();

        // Initialize filter parameters for view
        $cityId = null;
        $active = null;
        $search = null;
        $dateFrom = null;
        $dateTo = null;

        // Empty paginated result for backward compatibility
        $regions = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);

        return view('areasettings::region.index', compact(
            'regions',
            'cities',
            'languages',
            'search',
            'cityId',
            'active',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Get regions data for DataTables AJAX
     */
    public function datatable(Request $request)
    {
        $data = $this->regionAction->getDatatableData($request);
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $cities = CityResource::collection($this->cityService->getActiveCities())->resolve();
        try {
            $languages = $this->languageService->getAll();
            $selectedCityId = $request->get('city_id');

            return view('areasettings::region.form', compact('languages', 'cities', 'selectedCityId'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.regions.index')
                ->with('error', __('Error loading form'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegionRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->regionService->createRegion($validated);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Region created successfully'),
                    'redirect' => route('admin.area-settings.regions.index')
                ]);
            }

            return redirect()->route('admin.area-settings.regions.index')
                ->with('success', __('Region created successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error creating region: ') . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('Error creating region: ') . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $region = $this->regionService->getRegionById($id);
            $languages = $this->languageService->getAll();
            return view('areasettings::region.view', compact('region', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.regions.index')
                ->with('error', __('Region not found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $cities = CityResource::collection($this->cityService->getActiveCities())->resolve();
            $region = $this->regionService->getRegionById($id);
            return view('areasettings::region.form', compact('region', 'languages', 'cities'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.regions.index')
                ->with('error', __('Region not found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RegionRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $this->regionService->updateRegion($id, $validated);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Region updated successfully'),
                    'redirect' => route('admin.area-settings.regions.index')
                ]);
            }

            return redirect()->route('admin.area-settings.regions.index')
                ->with('success', __('Region updated successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error updating region: ') . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('Error updating region: ') . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $this->regionService->deleteRegion($id);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Region deleted successfully'),
                    'redirect' => route('admin.area-settings.regions.index')
                ]);
            }

            return redirect()->route('admin.area-settings.regions.index')
                ->with('success', __('Region deleted successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error deleting region: ') . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.area-settings.regions.index')
                ->with('error', __('Error deleting region: ') . $e->getMessage());
        }
    }

    /**
     * Change the status of the specified region.
     */
    public function changeStatus(Request $request, string $id)
    {
        // Validate the request
        $request->validate([
            'status' => 'required|in:1,2'
        ]);

        try {
            $region = $this->regionService->getRegionById($id);

            // Get the new status from request (1 = active, 2 = inactive)
            $newStatus = $request->input('status') == '1';

            // Check if status is actually changing
            if ($region->active == $newStatus) {
                return response()->json([
                    'success' => false,
                    'message' => __('areasettings::region.status_already_set')
                ], 422);
            }

            // Update the region status
            $this->regionService->updateRegion($id, ['active' => $newStatus]);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('areasettings::region.status_changed_successfully'),
                    'new_status' => $newStatus,
                    'status_text' => $newStatus ? __('areasettings::region.active') : __('areasettings::region.inactive'),
                    'status_class' => $newStatus ? 'badge-success' : 'badge-danger'
                ]);
            }

            return redirect()->route('admin.area-settings.regions.index')
                ->with('success', __('areasettings::region.status_changed_successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('areasettings::region.error_changing_status') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.area-settings.regions.index')
                ->with('error', __('areasettings::region.error_changing_status') . ': ' . $e->getMessage());
        }
    }
}
