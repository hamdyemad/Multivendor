<?php

namespace Modules\Order\app\Pipelines;

use Closure;

class ValidateProducts
{
    /**
     * Handle the pipeline.
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        // Validate products array
        if (empty($data['products']) || !is_array($data['products'])) {
            throw new \Exception(trans('order.validation.products_required'));
        }

        // Validate and normalize each product
        $normalizedProducts = [];
        foreach ($data['products'] as $product) {
            if (empty($product['vendor_product_variant_id']) || empty($product['quantity'])) {
                throw new \Exception(trans('order.invalid_product_data'));
            }

            if ($product['quantity'] <= 0) {
                throw new \Exception(trans('order.invalid_quantity'));
            }

            // Only pass minimal data - pipeline will fetch the rest from service
            $normalizedProducts[] = [
                'vendor_product_id' => $product['vendor_product_id'],
                'vendor_product_variant_id' => $product['vendor_product_variant_id'],
                'quantity' => $product['quantity'],
            ];
        }

        // Store normalized products in context
        $context['products'] = $normalizedProducts;

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
