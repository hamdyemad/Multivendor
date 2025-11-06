<?php

namespace Modules\AreaSettings\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\AreaSettings\app\Actions\SubRegionAction;
use Modules\AreaSettings\app\Http\Requests\SubRegionRequest;
use Modules\AreaSettings\app\Services\SubRegionService;
use Modules\AreaSettings\app\Services\RegionService;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\AreaSettings\app\Resources\RegionResource;

class SubRegionController extends Controller
{
    public function __construct(
        protected SubRegionService $subregionService,
        protected RegionService $regionService,
        protected LanguageService $languageService,
        protected SubRegionAction $subregionAction
    ) {
        $this->middleware('can:area.subregion.index')->only(['index']);
        $this->middleware('can:area.subregion.show')->only(['show']);
        $this->middleware('can:area.subregion.create')->only(['create', 'store']);
        $this->middleware('can:area.subregion.edit')->only(['edit', 'update']);
        $this->middleware('can:area.subregion.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get languages for table headers
        $languages = $this->languageService->getAll();

        // Get all regions for filter dropdown
        $regions = RegionResource::collection($this->regionService->getAllRegions())->resolve();

        return view('areasettings::subregion.index', compact(
            'regions',
            'languages'
        ));
    }

    /**
     * Get subregions data for DataTables AJAX
     */
    public function datatable(Request $request)
    {
        $data = $this->subregionAction->getDatatableData($request);
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $languages = $this->languageService->getAll();
            $selectedRegionId = $request->get('region_id');
            $regions = RegionResource::collection($this->regionService->getAllRegions())->resolve();

            return view('areasettings::subregion.form', compact('languages', 'regions', 'selectedRegionId'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.subregions.index')
                ->with('error', __('Error loading form'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubRegionRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->subregionService->createSubRegion($validated);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('areasettings::subregion.subregion_created'),
                    'redirect' => route('admin.area-settings.subregions.index')
                ]);
            }

            return redirect()->route('admin.area-settings.subregions.index')
                ->with('success', __('areasettings::subregion.subregion_created'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('areasettings::subregion.error_creating_subregion') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('areasettings::subregion.error_creating_subregion') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $subregion = $this->subregionService->getSubRegionById($id);
            $languages = $this->languageService->getAll();
            return view('areasettings::subregion.view', compact('subregion', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.subregions.index')
                ->with('error', __('areasettings::subregion.subregion_not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $regions = RegionResource::collection($this->regionService->getAllRegions())->resolve();
            $subregion = $this->subregionService->getSubRegionById($id);
            return view('areasettings::subregion.form', compact('subregion', 'languages', 'regions'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.subregions.index')
                ->with('error', __('areasettings::subregion.subregion_not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubRegionRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $this->subregionService->updateSubRegion($id, $validated);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('areasettings::subregion.subregion_updated'),
                    'redirect' => route('admin.area-settings.subregions.index')
                ]);
            }

            return redirect()->route('admin.area-settings.subregions.index')
                ->with('success', __('areasettings::subregion.subregion_updated'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('areasettings::subregion.error_updating_subregion') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('areasettings::subregion.error_updating_subregion') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $this->subregionService->deleteSubRegion($id);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('areasettings::subregion.subregion_deleted'),
                    'redirect' => route('admin.area-settings.subregions.index')
                ]);
            }

            return redirect()->route('admin.area-settings.subregions.index')
                ->with('success', __('areasettings::subregion.subregion_deleted'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('areasettings::subregion.error_deleting_subregion') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.area-settings.subregions.index')
                ->with('error', __('areasettings::subregion.error_deleting_subregion') . ': ' . $e->getMessage());
        }
    }
}
