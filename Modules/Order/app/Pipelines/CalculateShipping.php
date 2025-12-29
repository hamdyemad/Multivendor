<?php

namespace Modules\Order\app\Pipelines;

use App\Exceptions\OrderException;
use Closure;
use Modules\Order\app\Services\ShippingCalculationService;

class CalculateShipping
{
    public function __construct(
        private ShippingCalculationService $shippingCalculationService
    ) {}

    /**
     * Calculate shipping cost for order products
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        // Format cart items from products array
        $cartItems = [];
        if (isset($data['products']) && is_array($data['products'])) {
            foreach ($data['products'] as $product) {
                $cartItems[] = [
                    'category_id' => $product['category_id'] ?? null,
                    'category_name' => $product['category_name'] ?? null,
                    'product_id' => $product['vendor_product_id'] ?? null,
                    'quantity' => $product['quantity'] ?? 1,
                ];
            }
        }

        // Calculate shipping for existing customers with address
        if (isset($data['selected_customer_id']) && isset($data['customer_address_id']) && !empty($cartItems)) {
            try {
                $shippingResult = $this->shippingCalculationService->calculateShipping(
                    $data['selected_customer_id'],
                    $data['customer_address_id'],
                    $cartItems
                );

                // Update shipping value in data with calculated value
                $data['shipping'] = $shippingResult['shipping_cost'] ?? ($data['shipping'] ?? 0);
                $context['shipping_breakdown'] = $shippingResult['breakdown'] ?? [];
            } catch (\Exception $e) {
                // If shipping calculation fails, keep the submitted value or use 0
                $data['shipping'] = $data['shipping'] ?? 0;
                $context['shipping_breakdown'] = [];
            }
        }
        // Calculate shipping for external customers with city_id
        elseif (isset($data['external_city_id']) && !empty($cartItems)) {
            try {
                $shippingResult = $this->shippingCalculationService->calculateShipping(
                    null,
                    null,
                    $cartItems,
                    $data['external_city_id']
                );

                // Update shipping value in data with calculated value
                $data['shipping'] = $shippingResult['shipping_cost'] ?? ($data['shipping'] ?? 0);
                $context['shipping_breakdown'] = $shippingResult['breakdown'] ?? [];
            } catch (\Exception $e) {
                // If shipping calculation fails, keep the submitted value or use 0
                $data['shipping'] = $data['shipping'] ?? 0;
                $context['shipping_breakdown'] = [];
            }
        }

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
