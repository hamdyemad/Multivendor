<?php

namespace Modules\Order\app\Pipelines;

use Closure;

class AdjustCommissionForPoints
{
    /**
     * Handle the pipeline.
     *
     * Sets commission to 0 when points are used in the order.
     * If customer uses any points, commission should be 0 for all products.
     */
    public function handle($payload, Closure $next)
    {
        $context = $payload['context'];
        
        // Check if points were used
        $pointsCost = $context['points_cost'] ?? 0;
        
        if ($pointsCost <= 0) {
            // No points used, no adjustment needed
            return $next($payload);
        }
        
        \Log::info('AdjustCommissionForPoints: Points used, setting commission to 0', [
            'points_cost' => $pointsCost,
            'points_used' => $context['points_used'] ?? 0,
        ]);
        
        // Set commission to 0 for all products when points are used
        $productsData = $context['products_data'] ?? [];
        foreach ($productsData as &$product) {
            $originalCommission = $product['commission'] ?? 0;
            
            \Log::info('AdjustCommissionForPoints: Setting product commission to 0', [
                'product_id' => $product['vendor_product_id'],
                'original_commission' => $originalCommission,
            ]);
            
            $product['commission'] = 0;
        }
        
        // Update context with adjusted products data
        $context['products_data'] = $productsData;
        $payload['context'] = $context;
        
        return $next($payload);
    }
}
