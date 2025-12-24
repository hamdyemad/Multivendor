<?php

namespace Modules\Order\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\Order\app\DTOs\CartFilterDTO;
use Modules\Order\app\Http\Requests\Api\AddToCartRequest;
use Modules\Order\app\Http\Requests\Api\CheckCartRequest;
use Modules\Order\app\Services\Api\CartService;
use Modules\Order\app\Http\Resources\Api\CartResource;
use Modules\Order\app\Http\Requests\Api\AddBulkToCartRequest; 

class CartApiController extends Controller
{
    use Res;

    public function __construct(protected CartService $cartService)
    {}

    /**
     * Get customer's cart with pagination
     */
    public function list(Request $request)
    {
        $dto = CartFilterDTO::fromRequest($request);

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $customer = $request->user();
        $cart = $this->cartService->getCustomerCart($dto, $customer->id);
        $summary = $this->cartService->getCartSummary($customer->id);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            [
                'items' => CartResource::collection($cart),
                'totalProductPrice' => $summary ? round($summary['totalProductPrice'], 2) : 0,
                'totalTaxAmount' => $summary ? round($summary['totalTaxAmount'], 2) : 0,
                'totalPrice' => $summary ? round($summary['finalTotalPrice'], 2) : 0,
            ]
        );
    }

    /**
     * Add product to cart
     */
    public function addOrUpdate(AddToCartRequest $request)
    {
        $customer = $request->user();
        $validated = $request->validated();

        $this->cartService->addToCart($customer->id, $validated);

        return $this->sendRes(
            config('responses.item_added')[app()->getLocale()],
            true,
            [],
            [],
            201
        );
    }

    /**
     * Add bundle to cart with multiple items
     */
    public function addBulk(AddBulkToCartRequest $request)
    {
        $customer = $request->user();
        $validated = $request->validated();

        $this->cartService->addBulkToCart($customer->id, $validated['items']);

        return $this->sendRes(
            config('responses.item_added')[app()->getLocale()],
            true,
            [],
            [],
            201
        );
    }

    /**
     * Remove product from cart
     */
    public function remove(Request $request, $id)
    {
        $customer = $request->user();

        $this->cartService->removeFromCart($customer->id, $id);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true
        );
    }

    /**
     * Clear all items from cart
     */
    public function clear(Request $request)
    {
        $customer = $request->user();

        $this->cartService->clearCart($customer->id);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true
        );
    }

    /**
     * Check if product is in cart
     */
    public function check(CheckCartRequest $request)
    {
        $customer = $request->user();
        $validated = $request->validated();

        $isInCart = $this->cartService->isInCart(
            $customer->id,
            $validated['vendor_product_id'],
            $validated['vendor_product_variant_id'],
            $validated['type'] ?? 'product',
            $validated['bundle_id'] ?? null,
            $validated['occasion_id'] ?? null
        );

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            [
                'is_in_cart' => $isInCart,
                'vendor_product_id' => $validated['vendor_product_id'],
            ]
        );
    }

    public function count(Request $request)
    {
        $customer = $request->user();

        $count = $this->cartService->getCartCount($customer->id);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            [
                'count' => $count,
            ]
        );
    }

    public function summary(Request $request)
    {
        $customer = $request->user();

        $summary = $this->cartService->getCartSummary($customer->id);

        if (!$summary) {
            return $this->sendRes(
                config('responses.cart_not_found')[app()->getLocale()],
                true,
                []
            );
        }

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            [
                'total_product_price' => number_format($summary['totalProductPrice'], 2),
                'total_tax_amount' => number_format($summary['totalTaxAmount'], 2),
                'final_total_price' => number_format($summary['finalTotalPrice'], 2),
            ]
        );
    }
}
