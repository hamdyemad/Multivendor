<?php

namespace Modules\Order\app\Pipelines;

use Modules\SystemSetting\app\Models\PointsSetting;
use Modules\SystemSetting\app\Models\UserPoints;
use Modules\SystemSetting\app\Models\UserPointsTransaction;

class CalculatePointsUsagePipeline
{
    public function handle($payload, $next)
    {
        \Log::info('CalculatePointsUsagePipeline: Starting');

        $data = $payload['data'];
        $context = $payload['context'];
        $customerId = $data['selected_customer_id'] ?? null;
        
        // Properly evaluate use_point as boolean
        $usePoints = filter_var($data['use_point'] ?? false, FILTER_VALIDATE_BOOLEAN);
        
        // Initialize points usage in context
        $payload['context']['points_used'] = 0;
        $payload['context']['points_cost'] = 0;

        if (!$usePoints || !$customerId) {
            \Log::info('CalculatePointsUsagePipeline: Points usage not requested or no customer ID');
            return $next($payload);
        }

        // Get customer to find their currency first (needed for points calculation)
        $customer = \Modules\Customer\app\Models\Customer::find($customerId);
        if (!$customer || !$customer->country || !$customer->country->currency) {
            \Log::warning('CalculatePointsUsagePipeline: No currency found for customer');
            return $next($payload);
        }

        $currencyId = $customer->country->currency->id;
        $currencyCode = $customer->country->currency->code ?? 'EGP';

        // Get points setting for this currency to get conversion rate
        $pointsSetting = PointsSetting::where('currency_id', $currencyId)
            ->where('is_active', true)
            ->first();

        if (!$pointsSetting || $pointsSetting->points_value <= 0) {
            \Log::warning('CalculatePointsUsagePipeline: No points setting found for currency', [
                'currency_id' => $currencyId
            ]);
            return $next($payload);
        }

        // points_value = points per 1 currency unit (e.g., 15 points = 1 EGP)
        $pointsPerCurrency = (float) $pointsSetting->points_value;

        // Calculate full order total price
        $subtotal = $context['total_product_price'] ?? 0;
        $totalTax = $context['total_tax'] ?? 0;
        $totalFees = $context['total_fees'] ?? 0;
        $totalDiscounts = $context['total_discounts'] ?? 0;
        $shipping = (float) ($data['shipping'] ?? 0);
        
        // Calculate promo discount if applicable
        $promoCode = $context['promo_code'] ?? null;
        $promoDiscount = 0;
        if ($promoCode) {
            if ($promoCode->type === 'amount') {
                $promoDiscount = (float) $promoCode->value;
            } elseif ($promoCode->type === 'percent') {
                $promoDiscount = ($subtotal * (float) $promoCode->value) / 100;
            }
        }
        
        // Full order total
        $orderTotal = $subtotal + $totalTax + $totalFees + $shipping - $totalDiscounts - $promoDiscount;
        
        // Calculate how many points needed to cover the order
        $pointsNeededForOrder = $orderTotal * $pointsPerCurrency;

        // Get customer's available points
        $userPoints = UserPoints::where('user_id', $customerId)->first();
        
        if (!$userPoints) {
            \Log::warning('CalculatePointsUsagePipeline: No user points record found', [
                'customer_id' => $customerId
            ]);
            throw new \App\Exceptions\OrderException(
                trans('order::order.no_points_available_with_info', [
                    'available' => 0,
                    'needed' => number_format($pointsNeededForOrder, 0),
                    'order_total' => number_format($orderTotal, 2),
                    'currency' => $currencyCode
                ])
            );
        }

        // Get available points (total_points is the available balance)
        $availablePoints = (float) $userPoints->total_points;
        
        \Log::info('CalculatePointsUsagePipeline: User points found', [
            'total_points' => $userPoints->total_points,
            'available_points' => $availablePoints
        ]);

        if ($availablePoints <= 0) {
            \Log::info('CalculatePointsUsagePipeline: No available points');
            throw new \App\Exceptions\OrderException(
                trans('order::order.no_points_available_with_info', [
                    'available' => 0,
                    'needed' => number_format($pointsNeededForOrder, 0),
                    'order_total' => number_format($orderTotal, 2),
                    'currency' => $currencyCode
                ])
            );
        }

        \Log::info('CalculatePointsUsagePipeline: Order total calculated', [
            'subtotal' => $subtotal,
            'tax' => $totalTax,
            'order_total' => $orderTotal,
            'points_per_currency' => $pointsPerCurrency
        ]);
        
        // Check if customer has enough points to cover the full order
        if ($availablePoints >= $pointsNeededForOrder) {
            // Use exact points needed to cover the order (total becomes 0)
            $pointsToUse = $pointsNeededForOrder;
            $pointsCost = $orderTotal;
        } else {
            // Use all available points
            $pointsToUse = floor($availablePoints);
            $pointsCost = $pointsToUse / $pointsPerCurrency;
        }
        
        if ($pointsToUse <= 0) {
            \Log::info('CalculatePointsUsagePipeline: No points to use');
            throw new \App\Exceptions\OrderException(
                trans('order::order.no_points_available_with_info', [
                    'available' => number_format($availablePoints, 0),
                    'needed' => number_format($pointsNeededForOrder, 0),
                    'order_total' => number_format($orderTotal, 2),
                    'currency' => $currencyCode
                ])
            );
        }

        \Log::info('CalculatePointsUsagePipeline: Processing points', [
            'available_points' => $availablePoints,
            'points_needed_for_order' => $pointsNeededForOrder,
            'points_to_use' => $pointsToUse,
            'points_cost' => $pointsCost
        ]);

        // Update context for CalculateFinalTotal pipeline
        $payload['context']['points_used'] = $pointsToUse;
        $payload['context']['points_cost'] = $pointsCost;

        // Deduct from total_points and add to redeemed_points
        $userPoints->total_points -= $pointsToUse;
        $userPoints->redeemed_points += $pointsToUse;
        $userPoints->save();

        // Create transaction record
        $transaction = UserPointsTransaction::create([
            'user_id' => $customerId,
            'points' => -$pointsToUse,
            'type' => 'redeemed',
            'transactionable_type' => 'order_checkout',
            'transactionable_id' => 0,
        ]);

        $transaction->setTranslation('description', 'en', "Points redeemed for order checkout");
        $transaction->setTranslation('description', 'ar', "نقاط مستردة لإتمام الطلب");
        $transaction->save();

        // Store transaction ID for later update with order ID
        $payload['context']['points_transaction_id'] = $transaction->id;

        \Log::info('CalculatePointsUsagePipeline: Points processed', [
            'points_used' => $pointsToUse,
            'points_cost' => $pointsCost,
            'new_total_points' => $userPoints->total_points,
            'new_redeemed_points' => $userPoints->redeemed_points
        ]);

        return $next($payload);
    }
}
