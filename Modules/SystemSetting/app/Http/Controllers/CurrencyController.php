<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\SystemSetting\app\Actions\CurrencyAction;
use Modules\SystemSetting\app\Http\Requests\CurrencyRequest;
use Modules\SystemSetting\app\Services\CurrencyService;
use App\Services\LanguageService;

class CurrencyController extends Controller
{

    public function __construct(
        protected CurrencyService $currencyService, 
        protected LanguageService $languageService,
        protected CurrencyAction $currencyAction
    )
    {
        $this->middleware('can:system.currency.index')->only(['index']);
        $this->middleware('can:system.currency.show')->only(['show']);
        $this->middleware('can:system.currency.create')->only(['create', 'store']);
        $this->middleware('can:system.currency.edit')->only(['edit', 'update']);
        $this->middleware('can:system.currency.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $languages = $this->languageService->getAll();
        $data = [
            'languages' => $languages,
            'title' => __('systemsetting::currency.currencies_management'),
        ];
        return view('systemsetting::currency.index', $data);
    }

    /**
     * Get currencies data for DataTables AJAX
     */
    public function datatable(Request $request)
    {
        $data = $this->currencyAction->getDatatableData($request);
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getAll();
        $data = [
            'languages' => $languages,
            'title' => __('systemsetting::currency.add_currency'),
        ];
        return view('systemsetting::currency.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CurrencyRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->currencyService->createCurrency($validated);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::currency.created_successfully'),
                    'redirect' => route('admin.system-settings.currencies.index')
                ]);
            }
            
            return redirect()->route('admin.system-settings.currencies.index')
                ->with('success', __('systemsetting::currency.created_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::currency.error_creating') . ': ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', __('systemsetting::currency.error_creating') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $currency = $this->currencyService->getCurrencyById($id);
            $data = [
                'currency' => $currency,
                'languages' => $languages,
                'title' => __('systemsetting::currency.view_currency'),
            ];
            return view('systemsetting::currency.view', $data);
        } catch (\Exception $e) {
            return redirect()->route('admin.system-settings.currencies.index')
                ->with('error', __('systemsetting::currency.not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $currency = $this->currencyService->getCurrencyById($id);
            $data = [
                'currency' => $currency,
                'languages' => $languages,
                'title' => __('systemsetting::currency.edit_currency'),
            ];
            return view('systemsetting::currency.form', $data);
        } catch (\Exception $e) {
            return redirect()->route('admin.system-settings.currencies.index')
                ->with('error', __('systemsetting::currency.not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CurrencyRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $this->currencyService->updateCurrency($id, $validated);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::currency.updated_successfully'),
                    'redirect' => route('admin.system-settings.currencies.index')
                ]);
            }
            
            return redirect()->route('admin.system-settings.currencies.index')
                ->with('success', __('systemsetting::currency.updated_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::currency.error_updating') . ': ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', __('systemsetting::currency.error_updating') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $this->currencyService->deleteCurrency($id);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::currency.deleted_successfully'),
                    'redirect' => route('admin.system-settings.currencies.index')
                ]);
            }
            
            return redirect()->route('admin.system-settings.currencies.index')
                ->with('success', __('systemsetting::currency.deleted_successfully'));
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::currency.error_deleting') . ': ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->route('admin.system-settings.currencies.index')
                ->with('error', __('systemsetting::currency.error_deleting') . ': ' . $e->getMessage());
        }
    }
}
