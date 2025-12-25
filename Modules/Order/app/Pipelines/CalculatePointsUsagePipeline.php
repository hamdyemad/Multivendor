<?php

namespace Modules\Order\app\Pipelines;

use Modules\SystemSetting\app\Models\UserPoints;
use Modules\SystemSetting\app\Models\UserPointsTransaction;

class CalculatePointsUsagePipeline
{
    public function handle($payload, $next)
    {
        \Log::info('CalculatePointsUsagePipeline: Starting', ['payload' => $payload]);

        $data = $payload['data'];
        $customerId = $data['selected_customer_id'] ?? null;
        
        // Properly evaluate use_point as boolean
        $usePoints = filter_var($data['use_point'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $pointsToUse = (int) ($data['points_to_use'] ?? $data['point_amount'] ?? $data['points_amount'] ?? 0);
        
        // If user wants to use points but didn't specify amount, use available points up to order total
        if ($usePoints && $pointsToUse == 0 && $customerId) {
            $userPoints = UserPoints::where('user_id', $customerId)->first();
            if ($userPoints) {
                $totalPrice = $payload['context']['total_product_price'] ?? 100;
                $pointsToUse = min($userPoints->total_points, $totalPrice); // Use available points up to order total
            }
        }
        
        \Log::info('CalculatePointsUsagePipeline: Data extracted', [
            'customer_id' => $customerId,
            'use_points' => $usePoints,
            'points_to_use' => $pointsToUse,
            'available_fields' => array_keys($data)
        ]);

        // Initialize points usage
        $payload['data']['points_used'] = 0;
        $payload['data']['points_cost'] = 0;

        if ($usePoints && $pointsToUse > 0 && $customerId) {
            \Log::info('CalculatePointsUsagePipeline: Processing points usage');

            // Get customer's available points
            $userPoints = UserPoints::where('user_id', $customerId)->first();

            if ($userPoints && $userPoints->total_points >= $pointsToUse) {
                \Log::info('CalculatePointsUsagePipeline: User has sufficient points', [
                    'available' => $userPoints->total_points,
                    'requested' => $pointsToUse
                ]);

                // Calculate how much can be paid with points (1 point = 1 currency unit)
                $pointsUsed = $pointsToUse; // Number of points
                $pointsCost = $pointsToUse * 1; // Monetary value (1 point = 1 currency unit)
                
                // Get total from context - use product price as base
                $totalPrice = $payload['context']['total_product_price'] ?? 100;
                $shipping = $data['shipping'] ?? 0;
                $totalBeforeShipping = $totalPrice - $shipping;
                
                // Points can only be used for product cost, not shipping
                $maxPointsCost = min($pointsCost, $totalBeforeShipping);
                $maxPointsUsed = $maxPointsCost / 1; // Convert back to points

                $payload['data']['points_used'] = $maxPointsUsed;
                $payload['data']['points_cost'] = $maxPointsCost;
                
                // Store the total price in data for later pipelines
                if (!isset($payload['data']['total_price'])) {
                    $payload['data']['total_price'] = $totalPrice + $shipping;
                }
                // Reduce total_price by points_cost (not points_used)
                $payload['data']['total_price'] -= $maxPointsCost;

                // Update context as well for CreateOrder pipeline
                $payload['context']['total_price'] = $payload['data']['total_price'];
                $payload['context']['points_used'] = $maxPointsUsed;
                $payload['context']['points_cost'] = $maxPointsCost;

                // Deduct points from user's account
                $userPoints->total_points -= $maxPointsUsed;
                $userPoints->redeemed_points += $maxPointsUsed;
                $userPoints->save();

                // Create transaction record
                $transaction = UserPointsTransaction::create([
                    'user_id' => $customerId,
                    'points' => -$maxPointsUsed, // Negative for redemption
                    'type' => 'redeemed',
                    'transactionable_type' => 'order_checkout',
                    'transactionable_id' => 0, // Temporary value, will be updated after order creation
                ]);

                // Set transaction descriptions
                $transaction->setTranslation('description', 'en', "Points redeemed for order checkout");
                $transaction->setTranslation('description', 'ar', "نقاط مستردة لإتمام الطلب");

                \Log::info('CalculatePointsUsagePipeline: Points processed successfully', [
                    'points_used' => $maxPointsUsed,
                    'points_cost' => $maxPointsCost,
                    'new_total' => $payload['data']['total_price']
                ]);
            } else {
                \Log::warning('CalculatePointsUsagePipeline: Insufficient points or no user points record');
            }
        } else {
            \Log::info('CalculatePointsUsagePipeline: Points usage not requested or invalid data');
        }

        return $next($payload);
    }
}
