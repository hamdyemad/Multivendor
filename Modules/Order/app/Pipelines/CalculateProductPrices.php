<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Illuminate\Support\Facades\Log;
use Modules\CatalogManagement\app\Services\Api\ProductApiService;

class CalculateProductPrices
{
    public function __construct(
        private ProductApiService $productService,
    ) {}

    /**
     * Handle the pipeline.
     *
     * Fetches complete product data from service and prepares data for OrderProduct table.
     * Includes translations, vendor info, commission, and tax calculations.
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        $products = $context['products'];
        $totalProductPrice = 0;
        $totalTax = 0;
        $totalCommission = 0;
        $itemsCount = 0;
        $productsData = [];
        $productSalesData = [];

        foreach ($products as $formProduct) {
            // Log::info('============================');
            // Fetch complete product data from service using vendor_product_id
            $vendorProductId = $formProduct['vendor_product_id'];
            $vendorProductVariantId = $formProduct['vendor_product_variant_id'] ?? null;
            $quantity = (int) $formProduct['quantity'];

            // Get product details from service with all relationships for order creation
            $vendorProduct = $this->productService->findProductForOrder($vendorProductId);

            // Log::info('Product: '. print_r($vendorProduct, true));

            // Validate product data exists
            if (!$vendorProduct || !isset($vendorProduct['product'])) {
                throw new \Exception(trans('order::order.product_not_found', ['id' => $vendorProductId]));
            }

            // Extract necessary data
            $productId = $vendorProduct['product']['id'] ?? null;
            if (!$productId) {
                throw new \Exception(trans('order::order.product_id_not_found', ['id' => $vendorProductId]));
            }

            $productNameEn = $vendorProduct['product']['title_en'] ?? $vendorProduct['product']['title'] ?? 'Unknown Product';
            $productNameAr = $vendorProduct['product']['title_ar'] ?? $vendorProduct['product']['title'] ?? 'Unknown Product';
            $vendorId = $vendorProduct['vendor']['id'] ?? null;
            if (!$vendorId) {
                throw new \Exception(trans('order::order.vendor_id_not_found', ['id' => $vendorProductId]));
            }

            $price = (float) ($vendorProduct['variants'][0]['price'] ?? 0);
            $taxRate = (float) ($vendorProduct['tax']['tax_rate'] ?? 0);
            $taxNameEn = $vendorProduct['tax']['name_en'] ?? $vendorProduct['tax']['name'] ?? '';
            $taxNameAr = $vendorProduct['tax']['name_ar'] ?? $vendorProduct['tax']['name'] ?? '';
            $commission = (float) ($vendorProduct['product']['department']['commission'] ?? 0);
            $limitation = (int) ($vendorProduct['max_per_order'] ?? 0);

            // Calculate totals
            $productTotal = $price * $quantity;
            $tax = ($productTotal * $taxRate) / 100;
            $commissionAmount = ($productTotal * $commission) / 100;
            
            // Log::info('productTotal: '. $productId .' |' . $productTotal . '|');
            // Log::info('productTax: '. $productId .' |' . $tax . '|');
            // Log::info('productCommission: '. $productId .' |' . $commissionAmount . '|');

            $totalProductPrice += $productTotal;
            $totalTax += $tax;
            $totalCommission += $commissionAmount;
            $itemsCount += $quantity;

            $productsData[] = [
                'vendor_product_id' => $vendorProductId,
                'vendor_product_variant_id' => $vendorProductVariantId,
                'vendor_id' => $vendorId,
                'quantity' => $quantity,
                'price' => $price,
                'commission' => $commissionAmount,
                'translations' => [
                    'en' => [
                        'name' => $productNameEn,
                    ],
                    'ar' => [
                        'name' => $productNameAr,
                    ],
                ],
                'tax_id' => $vendorProduct['tax']['id'] ?? null,
                'tax_rate' => $taxRate,
                'tax_amount' => $tax,
                'tax_translations' => [
                    'en' => $taxNameEn,
                    'ar' => $taxNameAr,
                ],
                'total' => $productTotal,
                'limitation' => $limitation,
            ];

            $productSalesData[$vendorProductId] = $quantity;
        }

        $context['products_data'] = $productsData;
        $context['total_product_price'] = $totalProductPrice;
        $context['total_tax'] = $totalTax;
        $context['total_commission'] = $totalCommission;
        $context['items_count'] = $itemsCount;
        $context['product_sales_to_update'] = $productSalesData;
        // Log::info('context: '. print_r($context));
        // throw new \Exception('test');   
        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
