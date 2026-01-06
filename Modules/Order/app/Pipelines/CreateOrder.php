<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Modules\Order\app\Interfaces\OrderRepositoryInterface;

class CreateOrder
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}

    /**
     * Handle the pipeline.
     *
     * Creates the Order record in the database using repository.
     * This step persists the order with all calculated data from previous steps.
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        $customer = $context['customer'];

        // Prepare order data
        $promoCode = $context['promo_code'] ?? null;
        
        // Map promo code type from promocodes table (percent/amount) to orders table (percentage/fixed)
        $promoCodeType = null;
        if ($promoCode?->type) {
            $promoCodeType = match ($promoCode->type) {
                'percent' => 'percentage',
                'amount' => 'fixed',
                default => $promoCode->type,
            };
        }
        
        $orderData = [
            'customer_id' => $customer['id'],
            'customer_name' => $customer['name'],
            'customer_email' => $customer['email'],
            'customer_phone' => $customer['phone'],
            'customer_address' => $customer['address'],
            'order_from' => $this->orderFrom($data['order_from'] ?? 'web'),
            'payment_type' => $this->paymentType($data['payment_type'] ?? 'cash_on_delivery'),
            'shipping' => $context['shipping'],
            'total_tax' => $context['total_tax'],
            'total_fees' => $context['total_fees'],
            'total_discounts' => $context['total_discounts'],
            'total_product_price' => $context['total_product_price'],
            'items_count' => $context['items_count'],
            'total_price' => $context['total_price'],
            'stage_id' => 1,
            'country_id' => $customer['country_id'],
            'city_id' => $customer['city_id'],
            'region_id' => $customer['region_id'],
            'customer_promo_code_title' => $promoCode?->code,
            'customer_promo_code_value' => $promoCode?->value,
            'customer_promo_code_type' => $promoCodeType,
            'customer_promo_code_amount' => $context['promo_code_discount'],
            'points_used' => $context['points_used'] ?? 0,
            'points_cost' => $context['points_cost'] ?? 0,
        ];

        // Store order using repository
        $order = $this->orderRepository->storeOrder($orderData);

        // Update points transaction with order ID if points were used
        if (!empty($context['points_transaction_id'])) {
            \Modules\SystemSetting\app\Models\UserPointsTransaction::where('id', $context['points_transaction_id'])
                ->update(['transactionable_id' => $order->id]);
        }

        $context['order'] = $order;

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }


    private function paymentType($type)
    {
        return match ($type) {
            'cash_on_delivery' => 'cash_on_delivery',
            'online' => 'online',
            
            default => 'cash_on_delivery',
        };
    }

    private function orderFrom($type)
    {
        return match ($type) {
            'WEB' => 'web',
            'web' => 'web',
            'ANDROID' => 'android',
            'android' => 'android',
            'IOS' => 'ios',
            'ios' => 'ios',
            
            default => 'web',
        };
    }
}
