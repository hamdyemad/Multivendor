<?php

namespace Modules\Order\app\Interfaces\Api;

interface CartRepositoryInterface
{
    public function getCustomerCart(array $data, $customerId);
    public function addToCart($customerId, array $itemData);
    public function addBulkToCart($customerId, array $items);
    public function removeFromCart($customerId, $cartItemId);
    public function clearCart($customerId);
    public function isInCart($customerId, $vendorProductId, $vendorProductVariantId, $type = 'product', $bundleId = null, $occasionId = null);
    public function getCartCount($customerId);
    public function getCartSummary($customerId);
}
