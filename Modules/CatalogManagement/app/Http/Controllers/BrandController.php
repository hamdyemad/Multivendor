<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LanguageService;
use Modules\CatalogManagement\app\Services\BrandService;
use Modules\CatalogManagement\app\Http\Requests\BrandRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\CatalogManagement\app\Actions\BrandAction;
use App\Models\Attachment;

class BrandController extends Controller
{

    public function __construct(
        protected BrandService $brandService,
        protected LanguageService $languageService,
        protected BrandAction $brandAction
    ) {
        $this->middleware('can:brands.index')->only(['index', 'datatable', 'brandSearch']);
        $this->middleware('can:brands.create')->only(['create', 'store']);
        $this->middleware('can:brands.edit')->only(['edit', 'update']);
        $this->middleware('can:brands.delete')->only(['destroy']);
        $this->middleware('can:brands.show')->only(['show']);
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
            $response = $this->brandAction->getDataTable($data);

            Log::info('Brand Datatable Response', [
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
            Log::error('Brand Datatable Error: ' . $e->getMessage(), [
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
    public function brandSearch(Request $request)
    {
        return $this->brandService->searchForSelect2($request->q, $request->page);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get languages for table headers
        $languages = $this->languageService->getAll();
        return view('catalogmanagement::brand.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($lang, $countryCode)
    {
        $languages = $this->languageService->getAll();
        return view('catalogmanagement::brand.form', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($lang, $countryCode, BrandRequest $request)
    {
        $validated = $request->validated();

        try {
            $brand = $this->brandService->createBrand($validated);
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('brand.created_successfully'),
                    'redirect' => route('admin.brands.index')
                ]);
            }

            return redirect()->route('admin.brands.index')
                ->with('success', __('brand.created_successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('brand.error_creating') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('brand.error_creating') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($lang, $countryCode, string $id)
    {
        try {
            $brand = $this->brandService->getBrandById($id);
            $languages = $this->languageService->getAll();
            return view('catalogmanagement::brand.view', compact('brand', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.brands.index')
                ->with('error', __('brand.not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($lang, $countryCode, string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $brand = $this->brandService->getBrandById($id);
            return view('catalogmanagement::brand.form', compact('brand', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.brands.index')
                ->with('error', __('brand.not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($lang, $countryCode, BrandRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $brand = $this->brandService->updateBrand($id, $validated);
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('brand.updated_successfully'),
                    'redirect' => route('admin.brands.index')
                ]);
            }

            return redirect()->route('admin.brands.index')
                ->with('success', __('brand.updated_successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('brand.error_updating') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('brand.error_updating') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($lang, $countryCode, Request $request, string $id)
    {
        try {
            $this->brandService->deleteBrand($id);
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('brand.deleted_successfully'),
                    'redirect' => route('admin.brands.index')
                ]);
            }

            return redirect()->route('admin.brands.index')
                ->with('success', __('brand.deleted_successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('brand.error_deleting') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->route('admin.brands.index')
                ->with('error', __('brand.error_deleting') . ': ' . $e->getMessage());
        }
    }

    /**
     * Reorder brands
     */
    public function reorder($lang, $countryCode, Request $request)
    {
        try {
            $request->validate([
                'items' => 'required|array',
                'items.*.id' => 'required|integer|exists:brands,id',
                'items.*.sort_number' => 'required|integer|min:0'
            ]);

            \DB::beginTransaction();

            // Get all brands in current page sorted by current sort_number
            $allBrands = \Modules\CatalogManagement\app\Models\Brand::orderBy('sort_number', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // Create a map of brand IDs to their new positions
            $items = $request->items;
            $draggedItem = $items[0]; // We only drag one item at a time
            $draggedId = $draggedItem['id'];
            $targetSortNumber = $draggedItem['sort_number'];

            // Find the dragged brand in the collection
            $draggedBrand = $allBrands->firstWhere('id', $draggedId);
            if (!$draggedBrand) {
                throw new \Exception('Brand not found');
            }

            $oldSortNumber = $draggedBrand->sort_number;

            // Remove the dragged brand from collection
            $allBrands = $allBrands->reject(function($brand) use ($draggedId) {
                return $brand->id == $draggedId;
            })->values();

            // Find the target position in the collection
            $targetIndex = $allBrands->search(function($brand) use ($targetSortNumber) {
                return $brand->sort_number == $targetSortNumber;
            });

            // If target not found, insert at the end
            if ($targetIndex === false) {
                $targetIndex = $allBrands->count();
            }

            // Insert the dragged brand at the target position
            $allBrands->splice($targetIndex, 0, [$draggedBrand]);

            // Reassign sort numbers sequentially
            $sortNumber = 1;
            foreach ($allBrands as $brand) {
                if ($brand->sort_number != $sortNumber) {
                    $brand->update(['sort_number' => $sortNumber]);
                }
                $sortNumber++;
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('common.reorder_success') ?? 'Order updated successfully'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Brand reorder error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('common.reorder_error') ?? 'Failed to update order'
            ], 500);
        }
    }
}
