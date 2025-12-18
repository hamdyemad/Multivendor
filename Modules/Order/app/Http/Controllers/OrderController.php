<?php

namespace Modules\Order\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Order\app\Services\OrderService;
use Modules\Order\app\Http\Requests\CreateOrderRequest;
use Modules\Order\app\Http\Requests\StoreOrderRequest;
use Modules\Order\app\Http\Requests\UpdateOrderRequest;
use Modules\Order\app\Http\Requests\ChangeOrderStageRequest;
use Modules\Order\app\Http\Requests\AddProductToOrderRequest;
use Modules\Order\app\Http\Requests\AddExtraFeeDiscountRequest;
use Modules\Order\app\Http\Requests\CreateFulfillmentRequest;
use Modules\Order\app\Models\Order;
use App\Models\Language;
use Modules\Order\app\Http\Resources\Api\OrderStageResource;
use Modules\Order\app\Models\OrderProduct;
use Modules\Order\app\Models\OrderStage;
use Modules\Order\app\Services\OrderStageService;
use Modules\Vendor\app\Services\VendorService;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(
        OrderService $orderService,
        protected VendorService $vendorService,
        protected OrderStageService $orderStageService
    )
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orderStages = $this->orderStageService->getOrderStagesQuery()->get();
        $orderStages = OrderStageResource::collection($orderStages)->resolve();
        $languages = Language::all();
        $total_price =  number_format(OrderProduct::sum(\DB::raw('price * quantity')), 2);
        $orders_count = Order::latest()->count();
        $vendors = $this->vendorService->getAllVendors([]);
        $data = [
            'languages' => $languages,
            'orders_count' => $orders_count,
            'total_price' => $total_price,
            'vendors' => $vendors,
            'orderStages' => $orderStages,
        ];
        return view('order::orders.index', $data);
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
                'vendor_id' => $request->vendor ?? null,
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

                $vendors = $order->products->map(function ($orderProduct) {
                    return $orderProduct->vendorProduct->vendor ?? null;
                })->filter()->unique('id');

                $vendorsData = $vendors->map(function ($vendor) {
                    return [
                        'name' => $vendor->name,
                        'logo_url' => $vendor->logo ? asset('storage/' . $vendor->logo->path) : asset('assets/img/default.png')
                    ];
                })->values();

                $rowData = [
                    'index' => $index++,
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'customer_email' => $order->customer_email,
                    'customer_phone' => $order->customer_phone ?? '-',
                    'vendor' => $vendorsData,
                    'total_price' => $order->total_price . ' ' . currency(),
                    'total_product_price' => $order->total_product_price . ' ' . currency(),
                    'items_count' => $itemsCount,
                    'stage' => [
                        'id' => $order->stage?->id,
                        'slug' => $order->stage?->slug,
                        'name' => $order->stage?->name ?? '-',
                        'color' => $order->stage?->color ?? '-',
                    ],
                    'created_at' => $order->created_at,
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
    public function create($lang, $countryCode)
    {
        return view('order::orders.create');
    }

    /**
     * Store a newly created order
     */
    public function store($lang, $countryCode, StoreOrderRequest $request)
    {
        \Log::info($request->validated());
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
    public function show($lang, $countryCode, $id)
    {
        try {
            $order = $this->orderService->getOrderById($id);
            if (!$order) {
                return abort(404, trans('order::order.order_not_found'));
            }

            return view('order::orders.show', compact('order'));
        } catch (\Exception $e) {
            return abort(500, trans('order::order.error_loading_order'));
        }
    }

    /**
     * Change order stage
     */
    public function changeStage($lang, $countryCode, $id, ChangeOrderStageRequest $request)
    {
        try {
            $order = $this->orderService->changeOrderStage($id, $request->stage_id);

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
}
