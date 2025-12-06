<?php

namespace Modules\Order\app\Services;

use Modules\Order\app\Interfaces\OrderFulfillmentRepositoryInterface;
use Modules\Order\app\Models\Order;

class OrderFulfillmentService
{
    protected $fulfillmentRepository;

    public function __construct(OrderFulfillmentRepositoryInterface $fulfillmentRepository)
    {
        $this->fulfillmentRepository = $fulfillmentRepository;
    }

    /**
     * Get fulfillments for an order
     */
    public function getByOrder($orderId)
    {
        return $this->fulfillmentRepository->getByOrder($orderId);
    }

    /**
     * Get stock data for order
     */
    public function getStockDataForOrder($orderId)
    {
        return $this->fulfillmentRepository->getStockDataForOrder($orderId);
    }

    /**
     * Save allocations
     */
    public function saveAllocations($orderId, $allocations)
    {
        return $this->fulfillmentRepository->saveAllocations($orderId, $allocations);
    }

    /**
     * Validate allocations
     */
    public function validateAllocations($orderId)
    {
        return $this->fulfillmentRepository->validateAllocations($orderId);
    }

    /**
     * Update stock regions after fulfillment
     */
    public function updateStockRegions($orderId)
    {
        return $this->fulfillmentRepository->updateStockRegions($orderId);
    }

    /**
     * Check if order has complete allocations
     */
    public function hasCompleteAllocations($orderId)
    {
        try {
            $this->validateAllocations($orderId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get available regions for a product
     */
    public function getAvailableRegionsForProduct($orderProductId)
    {
        $stockData = $this->fulfillmentRepository->getStockDataForOrder(
            \Modules\Order\app\Models\OrderProduct::find($orderProductId)->order_id
        );

        $product = $stockData['stockData'][$orderProductId] ?? null;

        if (!$product) {
            return collect([]);
        }

        return collect($product['regions'])->filter(function ($region) {
            return $region['available_stock'] > 0;
        });
    }
}
