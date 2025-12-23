<?php

namespace Modules\Order\app\Pipelines;

use Modules\SystemSetting\app\Models\UserPoints;

class CalculatePointsUsagePipeline
{
    public function handle($data, $next)
    {
        $customerId = $data['customer_id'];
        $usePoints = $data['use_points'] ?? false;
        $pointsToUse = $data['points_to_use'] ?? 0;
        
        // Initialize points usage
        $data['points_used'] = 0;
        
        if ($usePoints && $pointsToUse > 0) {
            // Get customer's available points
            $userPoints = UserPoints::where('user_id', $customerId)->first();
            
            if ($userPoints && $userPoints->total_points >= $pointsToUse) {
                // Calculate how much can be paid with points (1 point = 1 currency unit)
                $pointsValue = $pointsToUse;
                $totalBeforeShipping = $data['total_price'] - ($data['shipping'] ?? 0);
                
                // Points can only be used for product cost, not shipping
                $maxPointsUsable = min($pointsValue, $totalBeforeShipping);
                
                $data['points_used'] = $maxPointsUsable;
                // Reduce total_price by points used
                $data['total_price'] -= $maxPointsUsable;
                
                // Deduct points from user's account
                $userPoints->total_points -= $maxPointsUsable;
                $userPoints->redeemed_points += $maxPointsUsable;
                $userPoints->save();
            }
        }
        
        return $next($data);
    }
}
