<?php

namespace Modules\Order\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Order\app\Http\Requests\OrderStageRequest;
use Modules\Order\app\Services\OrderStageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Language;

class OrderStageController extends Controller
{
    protected $orderStageService;

    public function __construct(OrderStageService $orderStageService)
    {
        $this->orderStageService = $orderStageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Language::all();
        return view('order::order-stages.index', compact('languages'));
    }

    /**
     * DataTable endpoint for order stages
     */
    public function datatable(Request $request)
    {
        try {
            // Get pagination parameters from DataTables
            $perPage = isset($request->per_page) && $request->per_page > 0 ? (int)$request->per_page : 10;
            $start = isset($request->start) && $request->start >= 0 ? (int)$request->start : 0;
            // Calculate page number from start offset
            $page = $perPage > 0 ? floor($start / $perPage) + 1 : 1;

            // Get filter parameters
            $filters = [
                'search' => $request->search ?? null,
                'active' => $request->active ?? null,
                'created_date_from' => $request->created_date_from ?? null,
                'created_date_to' => $request->created_date_to ?? null,
            ];

            // Get languages
            $languages = \App\Models\Language::all();

            // Get total and filtered counts
            $totalRecords = $this->orderStageService->getOrderStagesQuery([])->count();
            $filteredRecords = $this->orderStageService->getOrderStagesQuery($filters)->count();

            // Get order stages with pagination
            $orderStagesQuery = $this->orderStageService->getOrderStagesQuery($filters);
            $orderStages = $orderStagesQuery->paginate($perPage, ['*'], 'page', $page);

            // Return raw data - rendering will be handled by DataTables in the view
            $data = [];
            $index = $start + 1; // Start index from the correct offset
            foreach ($orderStages as $orderStage) {
                $rowData = [
                    'index' => $index++,
                    'id' => $orderStage->id,
                    'slug' => $orderStage->slug,
                    'color' => $orderStage->color,
                    'active' => $orderStage->active,
                    'is_system' => $orderStage->is_system,
                    'sort_order' => $orderStage->sort_order,
                    'created_at' => $orderStage->created_at,
                    'translations' => [],
                ];

                // Add translations for each language
                foreach ($languages as $language) {
                    $translation = $orderStage->translations->where('lang_id', $language->id)
                        ->where('lang_key', 'name')
                        ->first();
                    $rowData['translations'][$language->code] = [
                        'name' => $translation ? $translation->lang_value : '-',
                    ];
                }

                $data[] = $rowData;
            }

            return response()->json([
                'draw' => intval($request->input('draw', 1)), // Required for DataTables pagination
                'data' => $data,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'current_page' => $orderStages->currentPage(),
                'last_page' => $orderStages->lastPage(),
                'per_page' => $orderStages->perPage(),
                'total' => $orderStages->total(),
                'from' => $orderStages->firstItem(),
                'to' => $orderStages->lastItem()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading order stages: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($lang, $countryCode)
    {
        $languages = Language::all();
        return view('order::order-stages.form', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($lang, $countryCode, OrderStageRequest $request)
    {
        try {
            $orderStage = $this->orderStageService->createOrderStage($request->all());

            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('order::order_stage.order_stage_created'),
                    'data' => $orderStage
                ]);
            }

            return redirect()->route('admin.order-stages.index')
                ->with('success', __('order::order_stage.order_stage_created'));
        } catch (\Exception $e) {
            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('order::order_stage.error_creating_order_stage') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('order::order_stage.error_creating_order_stage') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($lang, $countryCode, $id)
    {
        $orderStage = $this->orderStageService->getOrderStageById($id);
        $languages = Language::all();
        return view('order::order-stages.show', compact('orderStage', 'languages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($lang, $countryCode, $id)
    {
        $orderStage = $this->orderStageService->getOrderStageById($id);
        $languages = Language::all();
        return view('order::order-stages.form', compact('orderStage', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($lang, $countryCode, $id, OrderStageRequest $request)
    {
        try {
            $orderStage = $this->orderStageService->updateOrderStage($id, $request->all());

            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('order::order_stage.order_stage_updated'),
                    'data' => $orderStage
                ]);
            }

            return redirect()->route('admin.order-stages.index')
                ->with('success', __('order::order_stage.order_stage_updated'));
        } catch (\Exception $e) {
            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('order::order_stage.error_updating_order_stage') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('order::order_stage.error_updating_order_stage') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($lang, $countryCode, $id)
    {
        try {
            $this->orderStageService->deleteOrderStage($id);
            return response()->json([
                'success' => true,
                'message' => __('order::order_stage.order_stage_deleted')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('order::order_stage.error_deleting_order_stage') . ': ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Toggle order stage status
     */
    public function toggleStatus($lang, $countryCode, $id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('order::order_stage.invalid_status')
                ], 422);
            }

            $orderStage = $this->orderStageService->getOrderStageById($id);
            
            if ($orderStage->is_system) {
                return response()->json([
                    'success' => false,
                    'message' => __('order::order_stage.cannot_change_status_system_stage')
                ], 422);
            }

            $newStatus = $request->input('status');

            // Check if status is actually changing
            if ($orderStage->active == $newStatus) {
                return response()->json([
                    'success' => false,
                    'message' => __('order::order_stage.status_already_set')
                ], 422);
            }

            $this->orderStageService->toggleOrderStageStatus($id);

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('order::order_stage.status_changed_successfully'),
                    'new_status' => $newStatus,
                    'redirect' => route('admin.order-stages.index')
                ]);
            }

            return redirect()->route('admin.order-stages.index')
                ->with('success', __('order::order_stage.status_changed_successfully'));

        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('order::order_stage.error_changing_status') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->with('error', __('order::order_stage.error_changing_status') . ': ' . $e->getMessage());
        }
    }
}
