<?php

namespace Modules\Order\app\Interfaces\Api;

interface OrderApiRepositoryInterface
{
    public function getCustomerOrders(array $filters);
    public function getCustomerOrderById(int $customerId, int $orderId);
    public function changeOrderStage(int $customerId, int $orderId, int $stageId, $allowedStage);
    public function validatePromoCode(string $code, ?int $customerId);
}
