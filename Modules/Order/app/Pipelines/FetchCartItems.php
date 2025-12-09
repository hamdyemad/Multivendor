<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Illuminate\Support\Facades\Auth;
use Modules\Order\app\DTOs\CartFilterDTO;
use Modules\Order\app\Services\Api\CartService;

class FetchCartItems
{
    public function __construct(
        private CartService $cartService
    ) {}

    /**
     * Fetch cart items from authenticated user and add to data
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        // Get cart items from authenticated user
        $dto = new CartFilterDTO();
        $cart = $this->cartService->getCustomerCart($dto, Auth::id())
            ->map(fn($item) => [
                'vendor_product_id' => $item->vendor_product_id,
                'vendor_product_variant_id' => $item->vendor_product_variant_id,
                'quantity' => $item->quantity,
            ])
            ->toArray();

        // Validate cart is not empty
        if (empty($cart)) {
            throw new \Exception(__('order.cart_is_empty'));
        }

        // Add cart items to data
        $data['products'] = $cart;

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
