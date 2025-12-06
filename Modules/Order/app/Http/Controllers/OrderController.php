<?php

namespace Modules\Order\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Order\app\Services\OrderService;
use Modules\Order\app\Http\Requests\CreateOrderRequest;
use Modules\Order\app\Http\Requests\StoreOrderRequest;
use Modules\Order\app\Http\Requests\UpdateOrderRequest;
use Modules\Order\app\Http\Requests\AddProductToOrderRequest;
use Modules\Order\app\Http\Requests\AddExtraFeeDiscountRequest;
use Modules\Order\app\Http\Requests\CreateFulfillmentRequest;
use Modules\Order\app\Models\Order;
use App\Models\Language;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Language::all();
        return view('order::orders.index', compact('languages'));
    }

    /**
     * DataTable endpoint for orders
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
                'created_date_from' => $request->created_date_from ?? null,
                'created_date_to' => $request->created_date_to ?? null,
                'stage_id' => $request->stage ?? null,
            ];

            // Get total records count
            $totalRecordsQuery = $this->orderService->getAllOrders([]);
            $totalRecords = $totalRecordsQuery->count();

            // Get filtered records count
            $filteredQuery = $this->orderService->getAllOrders($filters);
            $filteredRecords = $filteredQuery->count();

            // Get orders with pagination
            $ordersQuery = $this->orderService->getAllOrders($filters);
            $orders = $ordersQuery->paginate($perPage, ['*'], 'page', $page);

            // Return raw data - rendering will be handled by DataTables in the view
            $data = [];
            $index = $start + 1; // Start index from the correct offset
            foreach ($orders as $order) {
                // Count items in this order
                $itemsCount = $order->products ? $order->products->sum('quantity') : 0;

                $rowData = [
                    'index' => $index++,
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'customer_email' => $order->customer_email,
                    'total_price' => $order->total_price,
                    'items_count' => $itemsCount,
                    'stage' => $order->stage,
                    'created_at' => $order->created_at ? $order->created_at->format('Y-m-d H:i') : '-',
                ];

                $data[] = $rowData;
            }

            return response()->json([
                'draw' => intval($request->input('draw', 1)), // Required for DataTables pagination
                'data' => $data,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred. Please try again later.',
                'errors' => [],
                'data' => [
                    'class' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }

    /**
     * Show create order form
     */
    public function create()
    {
        return view('order::orders.create');
    }

    /**
     * Store a newly created order
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->orderService->createOrder($request->validated());

            return response()->json([
                'status' => true,
                'message' => trans('order::order.order_created'),
                'data' => [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'total_price' => $order->total_price,
                    'created_at' => $order->created_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => trans('order::order.error_creating_order'),
                'errors' => [$e->getMessage()],
            ], 422);
        }
    }

    /**
     * Display the specified order
     */
    public function show($id)
    {
        // Implementation here
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit($id)
    {
        // Implementation here
    }

    /**
     * Update the specified order
     */
    public function update($id, UpdateOrderRequest $request)
    {
        // Implementation here
    }

    /**
     * Delete the specified order
     */
    public function destroy($id)
    {
        // Implementation here
    }

    /**
     * Get order with all products
     */
    public function getOrderWithProducts($id)
    {
        // Implementation here
    }

    /**
     * Add product to order
     */
    public function addProduct($orderId, AddProductToOrderRequest $request)
    {
        // Implementation here
    }

    /**
     * Remove product from order
     */
    public function removeProduct($orderId, $orderProductId)
    {
        // Implementation here
    }

    /**
     * Change order stage
     */
    public function changeStage($id, Request $request)
    {
        try {
            $request->validate([
                'stage_id' => 'required|exists:order_stages,id',
            ]);

            $order = Order::findOrFail($id);
            $order->update(['stage_id' => $request->stage_id]);

            return response()->json([
                'status' => true,
                'message' => trans('order::order.stage_updated_successfully'),
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => trans('order::order.error_updating_stage'),
                'errors' => [$e->getMessage()]
            ], 422);
        }
    }

    /**
     * Update order status/stage
     */
    public function updateStatus($id, Request $request)
    {
        // Implementation here
    }

    /**
     * Add extra fee or discount to order
     */
    public function addExtraFeeDiscount($orderId, AddExtraFeeDiscountRequest $request)
    {
        // Implementation here
    }

    /**
     * Get order fulfillments
     */
    public function getFulfillments($orderId)
    {
        // Implementation here
    }

    /**
     * Create fulfillment for order product
     */
    public function createFulfillment($orderId, CreateFulfillmentRequest $request)
    {
        // Implementation here
    }

    /**
     * Update fulfillment status
     */
    public function updateFulfillmentStatus($fulfillmentId, Request $request)
    {
        // Implementation here
    }

    /**
     * Get order summary/totals
     */
    public function getSummary($id)
    {
        // Implementation here
    }

    /**
     * Print order invoice
     */
    public function printInvoice($id)
    {
        // Implementation here
    }

    /**
     * Export orders to CSV/Excel
     */
    public function export(Request $request)
    {
        // Implementation here
    }
}
