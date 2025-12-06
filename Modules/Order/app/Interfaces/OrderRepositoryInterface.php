<?php

namespace Modules\Order\app\Interfaces;

use Modules\Order\app\Models\Order;

interface OrderRepositoryInterface
{
    /**
     * Create a new order
     */
    public function createOrder(array $data);

    /**
     * Get all orders with filtering
     */
    public function getAllOrders(array $filters);

    /**
     * Get order by ID
     */
    public function getOrderById($id);

    /**
     * Update order
     */
    public function updateOrder($id, array $data);

    /**
     * Delete order
     */
    public function deleteOrder($id);

    /**
     * Get order with products
     */
    public function getOrderWithProducts($id);

    /**
     * Add product to order
     */
    public function addProductToOrder($orderId, array $productData);

    /**
     * Remove product from order
     */
    public function removeProductFromOrder($orderId, $orderProductId);

    /**
     * Update order status
     */
    public function updateOrderStatus($id, $stageId);

    /**
     * Add extra fee or discount
     */
    public function addExtraFeeDiscount($orderId, array $data);

    /**
     * Get order fulfillments
     */
    public function getOrderFulfillments($orderId);

    /**
     * Create fulfillment
     */
    public function createFulfillment($orderId, array $data);

    /**
     * Get datatable data
     */
    public function getDatatableData(array $filters);

    /**
     * Create order record (used in pipeline)
     */
    public function storeOrder(array $orderData);

    /**
     * Sync order products with taxes (used in pipeline)
     */
    public function syncOrderProducts(Order $order, array $productsData): void;

    /**
     * Sync order extras (fees and discounts) (used in pipeline)
     */
    public function syncOrderExtras(Order $order, array $fees, string $type): void;

    /**
     * Update product sales counters (used in pipeline)
     */
    public function updateProductSales(array $productSalesData);

    /**
     * Update pricing status to reserved (used in pipeline)
     */
    public function updatePricingStatus(int $priceId);
}
