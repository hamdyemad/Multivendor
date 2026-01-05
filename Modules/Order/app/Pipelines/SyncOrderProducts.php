<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Modules\Order\app\Interfaces\OrderRepositoryInterface;
use Modules\Order\app\Models\VendorOrderStage;

class SyncOrderProducts
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}


    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        $order = $context['order'];
        $productsData = $context['products_data'];
        $productShipping = $context['product_shipping'] ?? [];

        // Get total discount (promo code + points)
        $promoCodeDiscount = $context['promo_code_discount'] ?? 0;
        $pointsCost = $context['points_cost'] ?? 0;

        // Calculate total per vendor (price only, not shipping)
        $vendorTotals = [];
        $grandTotal = 0;
        
        foreach ($productsData as $product) {
            $vendorId = $product['vendor_id'];
            $productTotal = $product['price'] ?? 0;
            
            if (!isset($vendorTotals[$vendorId])) {
                $vendorTotals[$vendorId] = 0;
            }
            $vendorTotals[$vendorId] += $productTotal;
            $grandTotal += $productTotal;
        }

        // Merge shipping costs into products data
        foreach ($productsData as &$product) {
            $vendorProductId = $product['vendor_product_id'];
            if (isset($productShipping[$vendorProductId])) {
                $product['shipping_cost'] = $productShipping[$vendorProductId]['shipping_cost'];
            } else {
                $product['shipping_cost'] = 0;
            }
        }
        unset($product);

        $this->orderRepository->syncOrderProducts($order, $productsData);

        // Update vendor_order_stages with discount shares based on vendor's proportion of total
        // This runs after OrderProductObserver creates the vendor stages
        foreach ($vendorTotals as $vendorId => $vendorTotal) {
            // Calculate vendor's proportion of the total order
            $vendorProportion = $grandTotal > 0 ? $vendorTotal / $grandTotal : 0;
            
            // Distribute discounts based on proportion
            $promoCodeShare = round($promoCodeDiscount * $vendorProportion, 2);
            $pointsShare = round($pointsCost * $vendorProportion, 2);
            
            VendorOrderStage::where('order_id', $order->id)
                ->where('vendor_id', $vendorId)
                ->update([
                    'promo_code_share' => $promoCodeShare,
                    'points_share' => $pointsShare,
                ]);
        }

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
