<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Illuminate\Support\Facades\Log;

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
        // Use calculated shipping from CalculateShipping pipeline, or fallback to data
        $shipping = (float) ($data['shipping'] ?? 0);
        $tax = $context['total_tax'] ?? 0;
        $fees = $context['total_fees'] ?? 0;
        $discounts = $context['total_discounts'] ?? 0;
        $pointsCost = $context['points_cost'] ?? 0;

        Log::info('CalculateFinalTotal: Before calculation', [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'fees' => $fees,
            'discounts' => $discounts,
            'promo_discount' => $promoDiscount,
            'points_cost' => $pointsCost
        ]);

        $totalPrice = $subtotal + $shipping + $fees + $tax - $discounts - $promoDiscount - $pointsCost;

        Log::info('CalculateFinalTotal: After calculation', [
            'total_price' => $totalPrice
        ]);

        $context['subtotal'] = $subtotal;
        $context['shipping'] = $shipping;
        $context['total_price'] = max(0, $totalPrice);
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
