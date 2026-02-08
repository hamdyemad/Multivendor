<?php

namespace Modules\Order\app\Pipelines;

use Modules\SystemSetting\app\Models\PointsSetting;
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
        $customer = \Modules\Customer\app\Models\Customer::with('country.currency')->find($customerId);
        if (!$customer || !$customer->country || !$customer->country->currency) {
            \Log::warning('CalculatePointsUsagePipeline: No currency found for customer', [
                'customer_id' => $customerId
            ]);
            return $next($payload);
        }

        $currencyId = $customer->country->currency->id;
        $currencyCode = $customer->country->currency->code ?? 'EGP';
        
        \Log::info('CalculatePointsUsagePipeline: Customer loaded', [
            'customer_id' => $customerId,
            'currency_id' => $currencyId,
            'currency_code' => $currencyCode
        ]);

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

        // Get customer's available points using dynamic calculation from Customer model
        // This matches the /points/my-points API behavior
        $availablePoints = (float) $customer->available_points;
        
        \Log::info('CalculatePointsUsagePipeline: Customer points calculated', [
            'customer_id' => $customerId,
            'total_points' => $customer->total_points,
            'available_points' => $availablePoints,
            'earned_points' => $customer->earned_points,
            'redeemed_points' => $customer->redeemed_points,
            'expired_points' => $customer->expired_points
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
            'shipping' => $shipping,
            'order_total' => $orderTotal,
            'points_per_currency' => $pointsPerCurrency,
            'points_needed_for_full_order' => $pointsNeededForOrder,
            'available_points' => $availablePoints,
            'has_enough_points' => $availablePoints >= $pointsNeededForOrder
        ]);
        
        // Allow partial payment with points
        // Use all available points if not enough for full order
        $pointsToUse = min($availablePoints, $pointsNeededForOrder);
        $pointsCost = $pointsToUse / $pointsPerCurrency; // Convert points to currency

        \Log::info('CalculatePointsUsagePipeline: Processing points payment', [
            'available_points' => $availablePoints,
            'points_needed_for_order' => $pointsNeededForOrder,
            'points_to_use' => $pointsToUse,
            'points_cost' => $pointsCost,
            'is_full_payment' => $pointsToUse >= $pointsNeededForOrder,
            'remaining_to_pay' => max(0, $orderTotal - $pointsCost)
        ]);

        // Update context for CalculateFinalTotal pipeline
        $payload['context']['points_used'] = $pointsToUse;
        $payload['context']['points_cost'] = $pointsCost;

        // Create transaction record (negative points for redemption)
        // The Customer model accessors will automatically calculate the new balance
        $transaction = UserPointsTransaction::create([
            'user_id' => $customerId,
            'points' => -$pointsToUse, // Negative for redemption
            'type' => 'redeemed',
            'transactionable_type' => 'order_checkout',
            'transactionable_id' => 0, // Will be updated with order ID later
        ]);

        $transaction->setTranslation('description', 'en', "Points redeemed for order checkout");
        $transaction->setTranslation('description', 'ar', "نقاط مستردة لإتمام الطلب");
        $transaction->save();

        // Store transaction ID for later update with order ID
        $payload['context']['points_transaction_id'] = $transaction->id;

        // Recalculate available points after transaction
        $customer->refresh();
        $newAvailablePoints = $customer->available_points;

        \Log::info('CalculatePointsUsagePipeline: Points processed', [
            'points_used' => $pointsToUse,
            'points_cost' => $pointsCost,
            'previous_available_points' => $availablePoints,
            'new_available_points' => $newAvailablePoints,
            'transaction_id' => $transaction->id
        ]);

        return $next($payload);
    }
}
