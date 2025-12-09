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
        $promocode = $context['promo_code'] ?? null;
        $promoDiscount = $promocode ? $this->calculatePromoDiscount($context['total_product_price'], $promocode->value, $promocode->type) : 0;
        
        $subtotal = $context['total_product_price'];
        $shipping = $context['shipping'] ?? 0;
        $tax = $context['total_tax'] ?? 0;
        $fees = $context['total_fees'] ?? 0;
        $discounts = $context['total_discounts'] ?? 0;

        $totalPrice = $subtotal + $shipping + $fees + $tax - $discounts - $promoDiscount;

        $context['subtotal'] = $subtotal;
        $context['total_price'] = max(0, $totalPrice); // Ensure total is not negative
        $context['promo_code_discount'] = $promoDiscount;

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }

    private function calculatePromoDiscount($subtotal, $value, $type)
    {
        if ($type === 'amount') {
            return $value;
        } elseif ($type === 'percent') {
            return ($subtotal * $value) / 100;
        }
        return 0;
    }
}
