<?php

namespace Modules\Order\app\Actions;

use Illuminate\Support\Facades\Auth;
use Modules\Order\app\Models\Order;

class OrderQueryAction
{
    /**
     * Build order query for a customer with filters
     */
    public function handle(array $filters = [])
    {
        $query = Order::where('customer_id', Auth::id())
            ->with(['stage', 'products', 'products.vendorProduct', 'products.vendorProductVariant'])
            ->orderBy('created_at', 'desc')
            ->filter($filters);

        return $query;
    }
}
