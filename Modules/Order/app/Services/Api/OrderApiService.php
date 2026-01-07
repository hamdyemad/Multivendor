<?php

namespace Modules\Order\app\Services\Api;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Order\app\Interfaces\Api\OrderApiRepositoryInterface;
use Modules\Order\app\Pipelines\FetchCartItems;
use Modules\Order\app\Pipelines\ValidatePromoCode;
use Modules\Order\app\Pipelines\ValidateProducts;
use Modules\Order\app\Pipelines\FetchUserData;
use Modules\Order\app\Pipelines\CalculateApiProductPrices;
use Modules\Order\app\Pipelines\CalculateExtras;
use Modules\Order\app\Pipelines\CalculateFinalTotal;
use Modules\Order\app\Pipelines\CalculateShipping;
use Modules\Order\app\Pipelines\CreateOrder;
use Modules\Order\app\Pipelines\SyncOrderProducts;
use Modules\Order\app\Pipelines\UpdateProductSales;
use Modules\Order\app\Pipelines\EmptyCart;
use Modules\Order\app\Pipelines\CalculatePointsUsagePipeline;
use Modules\Order\app\Pipelines\ValidateDiscountAgainstRemaining;

class OrderApiService
{
    public function __construct(
        private OrderApiRepositoryInterface $orderRepository
    ) {}

    /**
     * Create a new order via API using pipeline pattern
     * Gets cart items and continues with pipeline
     */
    public function checkout(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Add authenticated user ID to data
            $data['selected_customer_id'] = Auth::id();
            $data['customer_type'] = "existing";

            $result = app(Pipeline::class)
                ->send([
                    'data' => $data,
                    'context' => [],
                ])
                ->through([
                    FetchCartItems::class,
                    ValidatePromoCode::class,
                    ValidateProducts::class,
                    FetchUserData::class,
                    CalculateApiProductPrices::class,
                    CalculateShipping::class,
                    CalculateExtras::class,
                    ValidateDiscountAgainstRemaining::class,
                    CalculatePointsUsagePipeline::class,
                    CalculateFinalTotal::class,
                    CreateOrder::class,
                    SyncOrderProducts::class,
                    UpdateProductSales::class,
                    // EmptyCart::class,
                ])
                ->thenReturn();

            return $result['context']['order'];
        });
    }

    /**
     * Get customer's orders with filtering
     */
    public function getMyOrders(array $filters)
    {
        return $this->orderRepository->getCustomerOrders($filters);
    }

    /**
     * Get order details by ID
     */
    public function getOrderDetails($orderId)
    {
        return $this->orderRepository->getCustomerOrderById(Auth::id(), $orderId);
    }

    /**
     * Cancel order
     */
    public function cancelOrder($orderId)
    {
        return $this->orderRepository->changeOrderStage(Auth::id(), $orderId, 4, 1);
    }

    /**
     * Return order
     */
    public function returnOrder($orderId)
    {
        return $this->orderRepository->changeOrderStage(Auth::id(), $orderId, 7, 3);
    }

    /**
     * Check promo code validity
     */
    public function checkPromoCode(string $code)
    {
        return $this->orderRepository->validatePromoCode($code, Auth::id());
    }
}
