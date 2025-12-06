<?php

namespace Modules\Order\app\Services;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Modules\Order\app\Interfaces\OrderRepositoryInterface;
use Modules\Order\app\Pipelines\ValidateProducts;
use Modules\Order\app\Pipelines\FetchUserData;
use Modules\Order\app\Pipelines\CalculateProductPrices;
use Modules\Order\app\Pipelines\CalculateExtras;
use Modules\Order\app\Pipelines\CalculateFinalTotal;
use Modules\Order\app\Pipelines\CreateOrder;
use Modules\Order\app\Pipelines\SyncOrderProducts;
use Modules\Order\app\Pipelines\SyncExtras;
use Modules\Order\app\Pipelines\UpdateProductSales;

class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    /**
     * Create a new order using pipeline pattern
     */
    public function createOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            $result = app(Pipeline::class)
                ->send([
                    'data' => $data,
                    'context' => [],
                ])
                ->through([
                    ValidateProducts::class,
                    FetchUserData::class,
                    CalculateProductPrices::class,
                    CalculateExtras::class,
                    CalculateFinalTotal::class,
                    CreateOrder::class,
                    SyncOrderProducts::class,
                    SyncExtras::class,
                    UpdateProductSales::class,
                ])
                ->thenReturn();

            return $result['context']['order'];
        });
    }

    /**
     * Get all orders with filtering
     */
    public function getAllOrders(array $filters)
    {
        return $this->orderRepository->getAllOrders($filters);
    }

    /**
     * Get order by ID
     */
    public function getOrderById($id)
    {
        return $this->orderRepository->getOrderById($id);
    }

    /**
     * Change order stage and update fulfillments
     */
    public function changeOrderStage($id, $stageId)
    {
        return $this->orderRepository->changeOrderStage($id, $stageId);
    }
}
