<?php

namespace Modules\Order\app\Actions;

use Modules\Order\app\Models\Cart;

class CartQueryAction
{
    /**
     * Build cart query for a customer with filters
     */
    public function handle($customerId, array $filters = [])
    {
        $query = Cart::query()
            ->byCustomer($customerId)
            ->with([
                'vendorProduct.product.category',
                'vendorProduct.vendor',
                'vendorProduct.taxes',
                'vendorProductVariant',
                'bundle.bundleProducts',
                'occasion.occasionProducts'
            ])
            ->orderBy('created_at', 'desc')
            ->filter($filters);

        return $query;
    }
}
