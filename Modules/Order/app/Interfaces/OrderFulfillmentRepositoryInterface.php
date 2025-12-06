<?php

namespace Modules\Order\app\Interfaces;

interface OrderFulfillmentRepositoryInterface
{
    /**
     * Get fulfillments for an order
     */
    public function getByOrder($orderId);

    /**
     * Get fulfillments with order products and regions
     */
    public function getByOrderWithRelations($orderId);

    /**
     * Create or update fulfillment
     */
    public function createOrUpdate($orderId, $orderProductId, $regionId, $quantity);

    /**
     * Delete fulfillment
     */
    public function delete($orderProductId, $regionId);

    /**
     * Get stock data for order
     */
    public function getStockDataForOrder($orderId);

    /**
     * Save allocations
     */
    public function saveAllocations($orderId, $allocations);

    /**
     * Validate allocations
     */
    public function validateAllocations($orderId);

    /**
     * Update stock regions after fulfillment
     */
    public function updateStockRegions($orderId);
}
