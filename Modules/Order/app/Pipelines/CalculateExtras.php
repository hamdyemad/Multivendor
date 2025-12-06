<?php

namespace Modules\Order\app\Pipelines;

use Closure;

class CalculateExtras
{
    /**
     * Handle the pipeline.
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];
        // Parse fees
        $fees = [];
        $totalFees = 0;
        if (isset($data['feesData']) && $data['feesData'] !== null && $data['feesData'] !== '') {
            $feesData = is_string($data['feesData']) ? json_decode($data['feesData'], true) : $data['feesData'];
            // Ensure feesData is an array and not empty
            if (is_array($feesData) && count($feesData) > 0) {
                foreach ($feesData as $fee) {
                    if (is_array($fee) && !empty($fee['amount']) && $fee['amount'] > 0) {
                        $fees[] = [
                            'reason' => $fee['reason'] ?? 'Additional Fee',
                            'amount' => (float)$fee['amount'],
                        ];
                        $totalFees += (float)$fee['amount'];
                    }
                }
            }
        }

        // Parse discounts
        $discounts = [];
        $totalDiscounts = 0;
        if (isset($data['discountsData']) && $data['discountsData'] !== null && $data['discountsData'] !== '') {
            $discountsData = is_string($data['discountsData']) ? json_decode($data['discountsData'], true) : $data['discountsData'];
            // Ensure discountsData is an array and not empty
            if (is_array($discountsData) && count($discountsData) > 0) {
                foreach ($discountsData as $discount) {
                    if (is_array($discount) && !empty($discount['amount']) && $discount['amount'] > 0) {
                        $discounts[] = [
                            'reason' => $discount['reason'] ?? 'Discount',
                            'amount' => (float)$discount['amount'],
                        ];
                        $totalDiscounts += (float)$discount['amount'];
                    }
                }
            }
        }

        $context['fees'] = $fees;
        $context['total_fees'] = $totalFees;
        $context['discounts'] = $discounts;
        $context['total_discounts'] = $totalDiscounts;
        $context['shipping'] = (float)($data['shipping'] ?? 0);

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
