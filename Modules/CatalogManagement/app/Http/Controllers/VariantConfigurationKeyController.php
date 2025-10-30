<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LanguageService;
use Modules\CatalogManagement\app\Services\VariantConfigurationKeyService;
use Modules\CatalogManagement\app\Http\Requests\VariantConfigurationKeyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\CatalogManagement\app\Actions\VariantConfigurationKeyAction;
use Modules\CatalogManagement\app\Http\Resources\VariantsConfigurationKeyResource;

class VariantConfigurationKeyController extends Controller
{

    public function __construct(
        protected VariantConfigurationKeyService $variantKeyService, 
        protected LanguageService $languageService,
        protected VariantConfigurationKeyAction $variantKeyAction
    ) {
    }

    /**
     * Datatable endpoint for server-side processing
     */
    public function datatable(Request $request)
    {
        // Handle search parameter - could be string (custom input) or array (DataTables)
        $search = $request->get('search');
        $searchValue = (is_array($search)) ? $search['value'] : $search;
        $data = [
            'page' => $request->get('page', 1),
            'draw' => $request->get('draw', 1),
            'start' => $request->get('start', 0),
            'length' => $request->get('length', 10),
            'per_page' => $request->get('per_page', 10),
            'orderColumnIndex' => $request->get('order')[0]['column'] ?? 0,
            'orderDirection' => $request->get('order')[0]['dir'] ?? 'desc',
            'search' => $searchValue,
            'active' => $request->get('active'),
            'parent_key_id' => $request->get('parent_key_id'),
            'created_date_from' => $request->get('created_date_from'),
            'created_date_to' => $request->get('created_date_to'),
        ];

        try {
            $response = $this->variantKeyAction->getDataTable($data);
            
            Log::info('VariantConfigurationKey Datatable Response', [
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
            Log::error('VariantConfigurationKey Datatable Error: ' . $e->getMessage(), [
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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get languages for table headers
        $languages = $this->languageService->getAll();
        // Get all variant keys for parent dropdown filter
        $variantKeys = $this->variantKeyService->getAllVariantConfigurationKeys([], 0);
        $variantKeys = VariantsConfigurationKeyResource::collection($variantKeys)->resolve();
        return view('catalogmanagement::variant-key.index', compact('languages', 'variantKeys'));
    }

    /**
     * Display tree view of variant configuration keys
     */
    public function tree()
    {
        $languages = $this->languageService->getAll();
        // Get all variant keys with their relationships
        $variantKeys = $this->variantKeyService->getAllVariantConfigurationKeys([], 0);
        // Build tree structure (only root items - children will be loaded via relationship)
        $treeData = $variantKeys->whereNull('parent_key_id');
        return view('catalogmanagement::variant-key.tree', compact('languages', 'treeData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getAll();
        $variantKeys = $this->variantKeyService->getAllVariantConfigurationKeys([], 0);
        $variantKeys = VariantsConfigurationKeyResource::collection($variantKeys)->resolve();
        return view('catalogmanagement::variant-key.form', compact('languages', 'variantKeys'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VariantConfigurationKeyRequest $request)
    {
        $validated = $request->validated();

        try {
            $variantKey = $this->variantKeyService->createVariantConfigurationKey($validated);   
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('variantkey.created_successfully'),
                    'redirect' => route('admin.variant-keys.index')
                ]);
            }
            
            return redirect()->route('admin.variant-keys.index')
                ->with('success', __('variantkey.created_successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('variantkey.error_creating') . ': ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', __('variantkey.error_creating') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $variantKey = $this->variantKeyService->getVariantConfigurationKeyById($id);
            $languages = $this->languageService->getAll();
            return view('catalogmanagement::variant-key.view', compact('variantKey', 'languages'));
        } catch (\Exception $e) {
            return redirect()->route('admin.variant-keys.index')
                ->with('error', __('variantkey.not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $languages = $this->languageService->getAll();
            $variantKeys = $this->variantKeyService->getAllVariantConfigurationKeys([], 0);
            $variantKeys = VariantsConfigurationKeyResource::collection($variantKeys)->resolve();
            $variantKey = $this->variantKeyService->getVariantConfigurationKeyById($id);
            return view('catalogmanagement::variant-key.form', compact('variantKey', 'languages', 'variantKeys'));
        } catch (\Exception $e) {
            return redirect()->route('admin.variant-keys.index')
                ->with('error', __('variantkey.not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VariantConfigurationKeyRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $variantKey = $this->variantKeyService->updateVariantConfigurationKey($id, $validated);
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('variantkey.updated_successfully'),
                    'redirect' => route('admin.variant-keys.index')
                ]);
            }
            
            return redirect()->route('admin.variant-keys.index')
                ->with('success', __('variantkey.updated_successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('variantkey.error_updating') . ': ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', __('variantkey.error_updating') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $this->variantKeyService->deleteVariantConfigurationKey($id);
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('variantkey.deleted_successfully'),
                    'redirect' => route('admin.variant-keys.index')
                ]);
            }
            
            return redirect()->route('admin.variant-keys.index')
                ->with('success', __('variantkey.deleted_successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('variantkey.error_deleting') . ': ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->route('admin.variant-keys.index')
                ->with('error', __('variantkey.error_deleting') . ': ' . $e->getMessage());
        }
    }
}
