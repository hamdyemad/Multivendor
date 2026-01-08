<?php

namespace Modules\Order\app\Interfaces;

use Modules\Order\app\Models\Order;

interface OrderRepositoryInterface
{
    public function getAllOrders(array $filters);
    public function getOrderById($id);
    public function changeOrderStage($id, $stageId);
    public function changeOrderProductStage($orderProductId, $stageId);
    public function storeOrder(array $orderData);
    public function syncOrderProducts(Order $order, array $productsData): void;
    public function syncOrderExtras(Order $order, array $fees, string $type, ?int $vendorId = null): void;
    public function updateProductSales(array $productSalesData);
    public function updatePricingStatus(int $priceId);
    public function deleteOrder($id): bool;
}
