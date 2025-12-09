<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Modules\CatalogManagement\app\Models\Promocode;
use Modules\Order\app\Interfaces\Api\OrderApiRepositoryInterface;

class ValidatePromoCode
{
    public function __construct(
        private OrderApiRepositoryInterface $orderRepository
    ) {}

    /**
     * Validate promo code if provided
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        // Get promo code ID from request (default null)
        $promoCodeId = $data['promo_code_id'] ?? null;
        $customerId = $data['selected_customer_id'] ?? null;

        $promoCode = null;
        $promoCodeDiscount = 0;

        // Only validate if promo code ID is provided
        if ($promoCodeId) {
            // Fetch the promo code by ID to get the code string
            $promoCodeModel = Promocode::find($promoCodeId);
            
            if ($promoCodeModel) {
                // Validate using the code string
                $promoCode = $this->orderRepository->validatePromoCode($promoCodeModel->code, $customerId);
                
                if ($promoCode) {
                    $promoCodeDiscount = (float) $promoCode->discount_value ?? 0;
                }
            }
        }

        // Add promo code data to context
        $context['promo_code'] = $promoCode;
        $context['promo_code_discount'] = $promoCodeDiscount;

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
