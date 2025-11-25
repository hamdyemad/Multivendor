<?php

namespace Modules\AreaSettings\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\AreaSettings\app\Actions\CountryAction;
use Modules\AreaSettings\app\Http\Requests\CountryRequest;
use Modules\AreaSettings\app\Services\CountryService;
use App\Services\LanguageService;
use Modules\SystemSetting\app\Services\CurrencyService;

class CountryController extends Controller
{

    public function __construct(
        protected CountryService $countryService,
        protected LanguageService $languageService,
        protected CountryAction $countryAction,
        protected CurrencyService $currencyService
    )
    {
        $this->middleware('can:area.country.index')->only(['index']);
        $this->middleware('can:area.country.show')->only(['show']);
        $this->middleware('can:area.country.create')->only(['create', 'store']);
        $this->middleware('can:area.country.edit')->only(['edit', 'update', 'changeStatus']);
        $this->middleware('can:area.country.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get languages for table headers
        $languages = $this->languageService->getAll();
        $data = [
            'languages' => $languages,
            'title' => __('areasettings::country.countries_management'),
        ];
        return view('areasettings::country.index', $data);
    }

    /**
     * Get countries data for DataTables AJAX
     */
    public function datatable(Request $request)
    {
        $data = $this->countryAction->getDatatableData($request);
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getAll();
        $currencies = $this->currencyService->getActiveCurrencies();
        $data = [
            'languages' => $languages,
            'currencies' => $currencies,
            'title' => __('areasettings::country.add_country'),
        ];
        return view('areasettings::country.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CountryRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->countryService->createCountry($validated);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Country created successfully'),
                    'redirect' => route('admin.area-settings.countries.index')
                ]);
            }

            return redirect()->route('admin.area-settings.countries.index')
                ->with('success', __('Country created successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error creating country: ') . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('Error creating country: ') . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $country = $this->countryService->getCountryById($id);
            $data = [
                'country' => $country,
                'languages' => $languages,
                'title' => __('areasettings::country.view_country'),
            ];
            return view('areasettings::country.view', $data);
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.countries.index')
                ->with('error', __('Country not found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $currencies = $this->currencyService->getActiveCurrencies();
            $country = $this->countryService->getCountryById($id);
            $data = [
                'country' => $country,
                'languages' => $languages,
                'currencies' => $currencies,
                'title' => __('areasettings::country.edit_country'),
            ];
            return view('areasettings::country.form', $data);
        } catch (\Exception $e) {
            return redirect()->route('admin.area-settings.countries.index')
                ->with('error', __('Country not found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CountryRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $this->countryService->updateCountry($id, $validated);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Country updated successfully'),
                    'redirect' => route('admin.area-settings.countries.index')
                ]);
            }

            return redirect()->route('admin.area-settings.countries.index')
                ->with('success', __('Country updated successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error updating country: ') . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('Error updating country: ') . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $this->countryService->deleteCountry($id);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Country deleted successfully'),
                    'redirect' => route('admin.area-settings.countries.index')
                ]);
            }

            return redirect()->route('admin.area-settings.countries.index')
                ->with('success', __('Country deleted successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error deleting country: ') . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.area-settings.countries.index')
                ->with('error', __('Error deleting country: ') . $e->getMessage());
        }
    }

    /**
     * Change the status of the specified country.
     */
    public function changeStatus(Request $request, string $id)
    {
        // Validate the request
        $request->validate([
            'status' => 'required|in:1,2'
        ]);

        try {
            $country = $this->countryService->getCountryById($id);

            // Get the new status from request (1 = active, 2 = inactive)
            $newStatus = $request->input('status') == '1';

            // Check if status is actually changing
            if ($country->active == $newStatus) {
                return response()->json([
                    'success' => false,
                    'message' => __('areasettings::country.status_already_set')
                ], 422);
            }

            // Update the country status
            $this->countryService->updateCountry($id, ['active' => $newStatus]);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('areasettings::country.status_changed_successfully'),
                    'new_status' => $newStatus,
                    'status_text' => $newStatus ? __('areasettings::country.active') : __('areasettings::country.inactive'),
                    'status_class' => $newStatus ? 'badge-success' : 'badge-danger'
                ]);
            }

            return redirect()->route('admin.area-settings.countries.index')
                ->with('success', __('areasettings::country.status_changed_successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('areasettings::country.error_changing_status') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.area-settings.countries.index')
                ->with('error', __('areasettings::country.error_changing_status') . ': ' . $e->getMessage());
        }
    }
}
