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
        $orderData = [
            'customer_id' => $customer['id'],
            'customer_name' => $customer['name'],
            'customer_email' => $customer['email'],
            'customer_phone' => $customer['phone'],
            'customer_address' => $customer['address'],
            'order_from' => $context['order_from'] ?? 'web',
            'payment_type' => $data['payment_type'] ?? 'cash_on_delivery',
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
        ];

        // Store order using repository
        $order = $this->orderRepository->storeOrder($orderData);

        $context['order'] = $order;

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
