<?php

namespace Modules\Order\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Order\app\Services\OrderService;
use Modules\Order\app\Http\Requests\StoreOrderRequest;

class OrderApiController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Create a new order via API
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->orderService->createOrder($request->validated());

            return response()->json([
                'status' => true,
                'message' => trans('order::order.order_created_successfully'),
                'data' => [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'customer_email' => $order->customer_email,
                    'total_price' => $order->total_price,
                    'items_count' => $order->items_count,
                    'stage_id' => $order->stage_id,
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
     * Get order details
     */
    public function show($id)
    {
        try {
            $order = $this->orderService->getOrderById($id);

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                    'errors' => [],
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Order retrieved successfully',
                'data' => [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'customer_email' => $order->customer_email,
                    'total_price' => $order->total_price,
                    'items_count' => $order->items_count,
                    'stage_id' => $order->stage_id,
                    'products' => $order->products,
                    'created_at' => $order->created_at,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found',
                'errors' => [$e->getMessage()],
            ], 404);
        }
    }
}
