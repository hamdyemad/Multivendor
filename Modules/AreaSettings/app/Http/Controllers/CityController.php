<?php

namespace Modules\AreaSettings\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\AreaSettings\app\Actions\CityAction;
use Modules\AreaSettings\app\Services\CityService;
use Modules\AreaSettings\app\Http\Requests\CityRequest;
use App\Services\LanguageService;
use Modules\AreaSettings\app\Services\CountryService;
use Illuminate\Http\Request;
use Modules\AreaSettings\app\Resources\CountryResource;

class CityController extends Controller
{

    public function __construct(
        protected CityService $cityService,
        protected CountryService $countryService,
        protected LanguageService $languageService,
        protected CityAction $cityAction
    ) {
        $this->middleware('can:area.city.index')->only(['index']);
        $this->middleware('can:area.city.show')->only(['show']);
        $this->middleware('can:area.city.create')->only(['create', 'store']);
        $this->middleware('can:area.city.edit')->only(['edit', 'update']);
        $this->middleware('can:area.city.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get languages for table headers
        $languages = $this->languageService->getAll();

        // Get countries for filter dropdown
        $countries = $this->countryService->getAllCountries();

        // Initialize filter parameters for view (to prevent undefined variable errors)
        $countryId = null;
        $active = null;
        $search = null;
        $dateFrom = null;
        $dateTo = null;

        // Empty paginated result for backward compatibility (DataTables will populate via AJAX)
        $cities = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);

        return view('areasettings::city.index', compact('languages', 'countries', 'countryId', 'active', 'search', 'dateFrom', 'dateTo', 'cities'));
    }

    /**
     * Get cities data for DataTables AJAX
     */
    public function datatable(Request $request)
    {
        $data = $this->cityAction->getDatatableData($request);
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $languages = $this->languageService->getAll();
        $countries = CountryResource::collection($this->countryService->getAllCountries())->resolve();
        // Get country_id from query parameter if passed
        $selectedCountryId = $request->get('country_id');
        return view('areasettings::city.form', compact('languages', 'countries', 'selectedCountryId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CityRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->cityService->createCity($validated);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('City created successfully'),
                    'redirect' => route('admin.area-settings.cities.index')
                ]);
            }

            return redirect()->route('admin.area-settings.cities.index')
                ->with('success', __('City created successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error creating city: ') . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('Error creating city: ') . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $city = $this->cityService->getCityById($id);
            $languages = $this->languageService->getAll();
            return view('areasettings::city.view', compact('city', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.cities.index')
                ->with('error', __('City not found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $countries = CountryResource::collection($this->countryService->getAllCountries())->resolve();
            $city = $this->cityService->getCityById($id);
            return view('areasettings::city.form', compact('city', 'languages', 'countries'));
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.cities.index')
                ->with('error', __('City not found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CityRequest $request, string $id)
    {
        $validated = $request->validated();

        \Log::info('Validated data', ['validated' => $validated]);

        try {
            $this->cityService->updateCity($id, $validated);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('City updated successfully'),
                    'redirect' => route('admin.area-settings.cities.index')
                ]);
            }

            return redirect()->route('admin.area-settings.cities.index')
                ->with('success', __('City updated successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error updating city: ') . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('Error updating city: ') . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $this->cityService->deleteCity($id);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('City deleted successfully'),
                    'redirect' => route('admin.area-settings.cities.index')
                ]);
            }

            return redirect()->route('admin.area-settings.cities.index')
                ->with('success', __('City deleted successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error deleting city: ') . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.area-settings.cities.index')
                ->with('error', __('Error deleting city: ') . $e->getMessage());
        }
    }
}
