<?php

namespace Modules\Order\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\Order\app\Actions\CartQueryAction;
use Modules\Order\app\Interfaces\Api\CartRepositoryInterface;
use Modules\Order\app\Models\Cart;
use Illuminate\Support\Facades\DB;

class CartRepository implements CartRepositoryInterface
{
    public function __construct(protected CartQueryAction $query, protected IsPaginatedAction $paginated)
    {}

    /**
     * Get all cart items for a customer with pagination
     */
    public function getCustomerCart(array $data, $customerId)
    {
        $query = $this->query->handle($customerId, $data);
        $result = $this->paginated->handle($query, $data['per_page'] ?? 15, $data['paginated'] ?? false);
        return $result;
    }

    public function addToCart($customerId, array $itemData)
    {
        return DB::transaction(function () use ($customerId, $itemData) {
            $existingCart = Cart::where('customer_id', $customerId)
                ->where('vendor_product_id', $itemData['vendor_product_id'])
                ->where('vendor_product_variant_id', $itemData['vendor_product_variant_id'] ?? null)
                ->where('type', $itemData['type'] ?? 'product')
                ->where('bundle_id', $itemData['bundle_id'] ?? null)
                ->where('occasion_id', $itemData['occasion_id'] ?? null)
                ->first();

            if ($existingCart) {
                $existingCart->update([
                    'quantity' => $itemData['quantity'],
                ]);
                return $existingCart;
            }

            return Cart::create([
                'customer_id' => $customerId,
                'vendor_product_id' => $itemData['vendor_product_id'],
                'vendor_product_variant_id' => $itemData['vendor_product_variant_id'] ?? null,
                'type' => $itemData['type'] ?? 'product',
                'bundle_id' => $itemData['bundle_id'] ?? null,
                'occasion_id' => $itemData['occasion_id'] ?? null,
                'quantity' => $itemData['quantity'] ?? 1,
            ]);
        });
    }

    /**
     * Remove a product from cart
     */
    public function removeFromCart($customerId, $cartItemId)
    {
        return DB::transaction(function () use ($customerId, $cartItemId) {
            $cartItem = Cart::where('customer_id', $customerId)->findOrFail($cartItemId);
            return $cartItem->delete();
        });
    }

    /**
     * Remove all items from cart
     */
    public function clearCart($customerId)
    {
        return DB::transaction(function () use ($customerId) {
            return Cart::where('customer_id', $customerId)->delete();
        });
    }

    /**
     * Check if product is in cart
     */
    public function isInCart($customerId, $vendorProductId, $vendorProductVariantId, $type = 'product', $bundleId = null, $occasionId = null): bool
    {
        return Cart::isInCart($customerId, $vendorProductId, $vendorProductVariantId, $type, $bundleId, $occasionId);
    }

    /**
     * Get cart count for customer (total quantity)
     */
    public function getCartCount($customerId): int
    {
        return Cart::getTotalItems($customerId);
    }

    /**
     * Get cart summary with totals
     */
    public function getCartSummary($customerId)
    {
        $carts = $this->query->handle($customerId)->get();

        if ($carts->isEmpty()) {
            return null;
        }

        $totalProductPrice = 0;
        $totalTaxAmount = 0;

        foreach ($carts as $cart) {
            $lineItemTotal = $this->calculateLineItemTotal($cart);
            $totalProductPrice += $lineItemTotal;

            // Calculate taxes
            if ($cart->vendorProduct && $cart->vendorProduct->tax) {
                $taxAmount = ($lineItemTotal * $cart->vendorProduct->tax->tax_rate) / 100;
                $totalTaxAmount += $taxAmount;
            }
        }

        $finalTotalPrice = $totalProductPrice + $totalTaxAmount;

        return [
            'totalProductPrice' => $totalProductPrice,
            'totalTaxAmount' => $totalTaxAmount,
            'finalTotalPrice' => $finalTotalPrice,
        ];
    }

    /**
     * Calculate line item total based on cart type
     */
    private function calculateLineItemTotal($cart)
    {
        if ($cart->type === 'product') {
            if ($cart->vendorProductVariant) {
                $price = $cart->vendorProductVariant->price ?? 0;
                return (float) $price * $cart->quantity;
            }
        }
        // elseif ($cart->type === 'bundle' && $cart->bundle) {
        //     // Get bundle product price
        //     // This assumes BundleProduct model exists
        //     $bundleProduct = DB::table('bundle_products')
        //         ->where('bundle_id', $cart->bundle_id)
        //         ->where('vendor_product_id', $cart->vendor_product_id)
        //         ->where('vendor_product_variant_id', $cart->vendor_product_variant_id)
        //         ->first();

        //     if ($bundleProduct) {
        //         return (float) $bundleProduct->price * $cart->quantity;
        //     }
        // } elseif ($cart->type === 'occasion' && $cart->occasion) {
        //     // Get occasion product price
        //     // This assumes OccasionProduct model exists
        //     $occasionProduct = DB::table('occasion_products')
        //         ->where('occasion_id', $cart->occasion_id)
        //         ->where('vendor_product_id', $cart->vendor_product_id)
        //         ->where('vendor_product_variant_id', $cart->vendor_product_variant_id)
        //         ->first();

        //     if ($occasionProduct) {
        //         $price = $occasionProduct->special_price ?? $occasionProduct->price;
        //         return (float) $price * $cart->quantity;
        //     }
        // }

        return 0;
    }
}
