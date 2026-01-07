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
                'vendorProduct.product.department',
                'vendorProduct.product.subCategory',
                'vendorProduct.vendor',
                'vendorProduct.taxes',
                'vendorProduct.variants',
                'vendorProductVariant.variantConfiguration.key',
                'vendorProductVariant.variantConfiguration.parent_data.key',
                'bundle.bundleProducts',
                'occasion.occasionProducts'
            ])
            ->orderBy('created_at', 'desc')
            ->filter($filters);

        return $query;
    }
}
