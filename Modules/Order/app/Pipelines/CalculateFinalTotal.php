<?php

namespace Modules\Order\app\Pipelines;

use Closure;

class CalculateFinalTotal
{
    /**
     * Handle the pipeline.
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        // Calculate final total
        $subtotal = $context['total_product_price'];
        $shipping = $context['shipping'];
        $tax = $context['total_tax'];
        $fees = $context['total_fees'];
        $discounts = $context['total_discounts'];

        $totalPrice = $subtotal + $shipping + $fees + $tax - $discounts;

        $context['subtotal'] = $subtotal;
        $context['total_price'] = max(0, $totalPrice); // Ensure total is not negative

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
