<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Modules\Order\app\Models\Order;

class UpdateOrder
{
    /**
     * Update existing order with calculated data
     *
     * @param array $payload
     * @param Closure $next
     * @return mixed
     */
    public function handle(array $payload, Closure $next)
    {
        $orderId = $payload['context']['order_id'];
        $order = Order::with('products')->findOrFail($orderId);
        $customer = $payload['context']['customer'];

        // Update order with new calculated values
        $order->update([
            'customer_id' => $customer['id'],
            'customer_name' => $customer['name'],
            'customer_email' => $customer['email'],
            'customer_phone' => $customer['phone'],
            'customer_address' => $customer['address'],
            'country_id' => $customer['country_id'],
            'city_id' => $customer['city_id'],
            'region_id' => $customer['region_id'],
            'total_product_price' => $payload['context']['total_product_price'],
            'total_tax' => $payload['context']['total_tax'],
            'total_fees' => $payload['context']['total_fees'],
            'total_discounts' => $payload['context']['total_discounts'],
            'shipping_cost' => $payload['context']['shipping_cost'] ?? $payload['context']['shipping'] ?? 0,
            'total_price' => $payload['context']['total_price'],
            'items_count' => $payload['context']['items_count'] ?? count($payload['context']['products'] ?? []),
        ]);

        // Delete existing product taxes first (to avoid foreign key issues)
        foreach ($order->products as $product) {
            $product->taxes()->delete();
        }
        
        // Delete existing products and extras to re-sync them
        $order->products()->delete();
        $order->extraFeesDiscounts()->delete();

        $payload['context']['order'] = $order->fresh();

        return $next($payload);
    }
}
