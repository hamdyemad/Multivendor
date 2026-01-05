<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use App\Exceptions\OrderException;
use Illuminate\Support\Facades\Log;

class ValidateDiscountAgainstRemaining
{
    /**
     * Validate that promo code + points discount doesn't exceed Bnaia's commission (remaining).
     * Bnaia covers the discounts from their commission, so discount cannot exceed commission.
     * 
     * Example:
     * - Order total = 4000 EGP
     * - Promo 50% = 2000 EGP discount
     * - Commission 15% = 600 EGP (Bnaia's remaining)
     * - If discount (2000) > commission (600) → Error
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        // Get totals from context
        $totalProductPrice = $context['total_product_price'] ?? 0; // Price before tax
        $totalTax = $context['total_tax'] ?? 0;
        $totalCommission = $context['total_commission'] ?? 0; // This is Bnaia's commission amount
        $shipping = (float) ($data['shipping'] ?? 0);
        
        // Calculate total with shipping (price including tax + shipping)
        $totalWithTax = $totalProductPrice + $totalTax;
        $totalWithShipping = $totalWithTax + $shipping;
        
        // Get promo code discount
        $promoCode = $context['promo_code'] ?? null;
        $promoDiscount = 0;
        if ($promoCode) {
            if ($promoCode->type === 'amount') {
                $promoDiscount = (float) $promoCode->value;
            } elseif ($promoCode->type === 'percent') {
                $promoDiscount = ($totalWithTax * (float) $promoCode->value) / 100;
            }
        }
        
        // Get points cost (estimate max points that could be used)
        $usePoints = filter_var($data['use_point'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $maxPointsCost = 0;
        
        if ($usePoints) {
            $customerId = $data['selected_customer_id'] ?? null;
            if ($customerId) {
                $customer = \Modules\Customer\app\Models\Customer::find($customerId);
                if ($customer && $customer->country && $customer->country->currency) {
                    $currencyId = $customer->country->currency->id;
                    $pointsSetting = \Modules\SystemSetting\app\Models\PointsSetting::where('currency_id', $currencyId)
                        ->where('is_active', true)
                        ->first();
                    
                    if ($pointsSetting && $pointsSetting->points_value > 0) {
                        $userPoints = \Modules\SystemSetting\app\Models\UserPoints::where('user_id', $customerId)->first();
                        if ($userPoints && $userPoints->total_points > 0) {
                            // Max points cost = available points / points per currency
                            $maxPointsCost = $userPoints->total_points / $pointsSetting->points_value;
                            
                            // Points cost cannot exceed order total after promo discount
                            $orderTotalAfterPromo = $totalWithShipping - $promoDiscount;
                            if ($maxPointsCost > $orderTotalAfterPromo) {
                                $maxPointsCost = $orderTotalAfterPromo;
                            }
                        }
                    }
                }
            }
        }
        
        // Total discount = promo discount + points cost
        $totalDiscount = $promoDiscount + $maxPointsCost;
        
        // Bnaia's remaining is the commission amount
        // Bnaia covers the discounts from their commission
        $bnaiaRemaining = $totalCommission;
        
        Log::info('ValidateDiscountAgainstRemaining: Checking discount limits', [
            'total_with_shipping' => $totalWithShipping,
            'total_commission' => $totalCommission,
            'bnaia_remaining' => $bnaiaRemaining,
            'promo_discount' => $promoDiscount,
            'max_points_cost' => $maxPointsCost,
            'total_discount' => $totalDiscount,
        ]);
        
        // Validate: total discount should not exceed Bnaia's commission (remaining)
        if ($totalDiscount > $bnaiaRemaining) {
            // Get currency for error message
            $currencyCode = 'EGP';
            $customerId = $data['selected_customer_id'] ?? null;
            if ($customerId) {
                $customer = \Modules\Customer\app\Models\Customer::find($customerId);
                if ($customer && $customer->country && $customer->country->currency) {
                    $currencyCode = $customer->country->currency->code ?? 'EGP';
                }
            }
            
            throw new OrderException(
                trans('order::order.discount_exceeds_commission', [
                    'total_discount' => number_format($totalDiscount, 2),
                    'max_discount' => number_format($bnaiaRemaining, 2),
                    'currency' => $currencyCode
                ])
            );
        }
        
        // Store in context for later use
        $context['bnaia_remaining'] = $bnaiaRemaining;
        $context['total_discount_amount'] = $totalDiscount;
        
        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
