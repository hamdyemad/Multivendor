<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Illuminate\Support\Facades\Auth;
use Modules\Order\app\Models\Cart;

class EmptyCart
{
    /**
     * Empty customer's cart after successful order creation
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        // Get authenticated customer ID
        $customerId = Auth::id();

        // Delete all cart items for this customer
        Cart::where('customer_id', $customerId)->delete();

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
