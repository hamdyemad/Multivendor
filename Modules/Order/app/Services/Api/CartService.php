<?php

namespace Modules\Order\app\Services\Api;

use Modules\Order\app\DTOs\CartFilterDTO;
use Modules\Order\app\Interfaces\Api\CartRepositoryInterface;

class CartService
{
    protected $cartRepository;

    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function getCustomerCart(CartFilterDTO $dto, $customerId)
    {
        return $this->cartRepository->getCustomerCart($dto->toArray(), $customerId);
    }

    public function addToCart($customerId, array $itemData)
    {
        return $this->cartRepository->addToCart($customerId, $itemData);
    }

    public function addBulkToCart($customerId, array $items)
    {
        return $this->cartRepository->addBulkToCart($customerId, $items);
    }

    public function removeFromCart($customerId, $cartItemId)
    {
        return $this->cartRepository->removeFromCart($customerId, $cartItemId);
    }

    public function clearCart($customerId)
    {
        return $this->cartRepository->clearCart($customerId);
    }

    public function isInCart($customerId, $vendorProductId, $vendorProductVariantId = null, $type = 'product', $bundleId = null, $occasionId = null): bool
    {
        return $this->cartRepository->isInCart($customerId, $vendorProductId, $vendorProductVariantId, $type, $bundleId, $occasionId);
    }

    public function getCartCount($customerId): int
    {
        return $this->cartRepository->getCartCount($customerId);
    }

    public function getCartSummary($customerId)
    {
        return $this->cartRepository->getCartSummary($customerId);
    }
}
