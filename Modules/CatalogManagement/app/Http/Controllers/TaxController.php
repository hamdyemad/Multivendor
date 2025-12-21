<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LanguageService;
use Modules\CatalogManagement\app\Services\TaxService;
use Modules\CatalogManagement\app\Http\Requests\TaxRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\CatalogManagement\app\Actions\TaxAction;

class TaxController extends Controller
{

    public function __construct(
        protected TaxService $taxService,
        protected LanguageService $languageService,
        protected TaxAction $taxAction
    ) {
        $this->middleware('can:taxes.index')->only(['index', 'datatable', 'taxSearch']);
        $this->middleware('can:taxes.create')->only(['create', 'store']);
        $this->middleware('can:taxes.edit')->only(['edit', 'update']);
        $this->middleware('can:taxes.delete')->only(['destroy']);
        $this->middleware('can:taxes.show')->only(['show']);
    }

    /**
     * Datatable endpoint for server-side processing
     */
    public function datatable(Request $request)
    {
        // Handle search parameter - could be string (custom input) or array (DataTables)
        $search = $request->get('search');
        if (is_array($search)) {
            $searchValue = $search['value'] ?? null;
        } else {
            $searchValue = $search;
        }

        $data = [
            'page' => $request->get('page', 1),
            'draw' => $request->get('draw', 1),
            'start' => $request->get('start', 0),
            'length' => $request->get('length', 10),
            'orderColumnIndex' => $request->get('order')[0]['column'] ?? 0,
            'orderDirection' => $request->get('order')[0]['dir'] ?? 'desc',
            'search' => $searchValue,
            'active' => $request->get('active'),
            'created_date_from' => $request->get('created_date_from'),
            'created_date_to' => $request->get('created_date_to'),
        ];

        try {
            $response = $this->taxAction->getDataTable($data);

            Log::info('Tax Datatable Response', [
                'data_count' => count($response['data']),
                'totalRecords' => $response['totalRecords'],
                'filteredRecords' => $response['filteredRecords']
            ]);

            return response()->json([
                'draw' => $data['draw'],
                'data' => $response['data'],
                'recordsTotal' => $response['totalRecords'],
                'recordsFiltered' => $response['filteredRecords'],
                'current_page' => $response['dataPaginated']->currentPage(),
                'last_page' => $response['dataPaginated']->lastPage(),
                'per_page' => $response['dataPaginated']->perPage(),
                'total' => $response['dataPaginated']->total(),
                'from' => $response['dataPaginated']->firstItem(),
                'to' => $response['dataPaginated']->lastItem()
            ]);
        } catch (\Exception $e) {
            Log::error('Tax Datatable Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'draw' => $data['draw'],
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search endpoint for Select2
     */
    public function taxSearch(Request $request)
    {
        return $this->taxService->searchForSelect2($request->q, $request->page);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get languages for table headers
        $languages = $this->languageService->getAll();
        return view('catalogmanagement::tax.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($lang, $countryCode)
    {
        $languages = $this->languageService->getAll();
        return view('catalogmanagement::tax.form', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($lang, $countryCode, TaxRequest $request)
    {
        $validated = $request->validated();

        try {
            $tax = $this->taxService->createTax($validated);
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Tax created successfully'),
                    'redirect' => route('admin.taxes.index')
                ]);
            }

            return redirect()->route('admin.taxes.index')
                ->with('success', __('Tax created successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error creating tax') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('Error creating tax') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($lang, $countryCode, string $id)
    {
        try {
            $tax = $this->taxService->getTaxById($id);
            $languages = $this->languageService->getAll();
            return view('catalogmanagement::tax.view', compact('tax', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.taxes.index')
                ->with('error', __('Tax not found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($lang, $countryCode, string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $tax = $this->taxService->getTaxById($id);
            return view('catalogmanagement::tax.form', compact('tax', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.taxes.index')
                ->with('error', __('Tax not found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($lang, $countryCode, TaxRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $tax = $this->taxService->updateTax($id, $validated);
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Tax updated successfully'),
                    'redirect' => route('admin.taxes.index')
                ]);
            }

            return redirect()->route('admin.taxes.index')
                ->with('success', __('Tax updated successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error updating tax') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('Error updating tax') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($lang, $countryCode, Request $request, string $id)
    {
        try {
            $this->taxService->deleteTax($id);
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Tax deleted successfully'),
                    'redirect' => route('admin.taxes.index')
                ]);
            }

            return redirect()->route('admin.taxes.index')
                ->with('success', __('Tax deleted successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Error deleting tax') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.taxes.index')
                ->with('error', __('Error deleting tax') . ': ' . $e->getMessage());
        }
    }
}
