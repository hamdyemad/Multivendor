<?php

namespace Modules\Order\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\Order\app\Services\Api\OrderApiService;
use Modules\Order\app\Http\Requests\Api\CheckoutRequest;
use Modules\Order\app\Http\Requests\Api\CheckPromoCodeRequest;
use Modules\Order\app\Http\Resources\Api\OrderResource;
use Modules\Order\app\Http\Resources\Api\OrderDetailResource;
use Modules\Order\app\Http\Resources\Api\PromoCodeResource;
use Modules\Order\app\DTOs\OrderFilterDTO;

class OrderApiController extends Controller
{
    use Res;

    public function __construct(
        private OrderApiService $orderApiService
    ) {}

    /**
     * Create a new order via API (Checkout)
     */
    public function checkout(CheckoutRequest $request)
    {
        $order = $this->orderApiService->checkout($request->validated());

        return $this->sendRes(
            config('responses.order_created_successfully')[app()->getLocale()],
            true,
            new OrderResource($order),
            [],
            201
        );
    }

    /**
     * Get customer's orders
     */
    public function myOrders(Request $request)
    {
        $dto = OrderFilterDTO::fromRequest($request);

        $orders = $this->orderApiService->getMyOrders($dto->toArray());

        return $this->sendRes(
            config('responses.orders_retrieved_successfully')[app()->getLocale()] ?? config('responses.success')[app()->getLocale()],
            true,
            OrderResource::collection($orders)
        );
    }

    /**
     * Get order details
     */
    public function show($orderId)
    {
        $order = $this->orderApiService->getOrderDetails($orderId);

        return $this->sendRes(
            config('responses.order_retrieved_successfully')[app()->getLocale()] ?? config('responses.success')[app()->getLocale()],
            true,
            new OrderDetailResource($order)
        );
    }

    /**
     * Cancel order
     */
    public function cancel($orderId)
    {
        $order = $this->orderApiService->cancelOrder($orderId);

        return $this->sendRes(
            config('responses.order_cancelled_successfully')[app()->getLocale()],
            true,
            new OrderResource($order)
        );
    }

    /**
     * Return order
     */
    public function return($orderId)
    {
        $order = $this->orderApiService->returnOrder($orderId);

        return $this->sendRes(
            config('responses.order_returned_successfully')[app()->getLocale()],
            true,
            new OrderResource($order)
        );
    }

    /**
     * Check promo code validity
     */
    public function checkPromoCode(CheckPromoCodeRequest $request)
    {
        $promoCode = $this->orderApiService->checkPromoCode($request->input('code'));

        if (!$promoCode) {
            return $this->sendRes(
                config('responses.promo_code_not_found')[app()->getLocale()],
                false,
                [],
                [],
                404
            );
        }

        return $this->sendRes(
            config('responses.promo_code_found_successfully')[app()->getLocale()],
            true,
            new PromoCodeResource($promoCode)
        );
    }
}
