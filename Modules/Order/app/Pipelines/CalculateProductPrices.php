<?php

namespace Modules\Order\app\Pipelines;

use Closure;
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
            // Fetch complete product data from service using vendor_product_id
            $vendorProductId = $formProduct['vendor_product_id'];
            $vendorProductVariantId = $formProduct['vendor_product_variant_id'] ?? null;
            $quantity = (int) $formProduct['quantity'];

            // Get product details from service with all relationships for order creation
            $vendorProduct = $this->productService->findProductForOrder($vendorProductId);

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

            // Price from database includes tax - this is the unit price with tax
            // Find the correct variant price based on vendor_product_variant_id
            $priceWithTax = 0;
            if ($vendorProductVariantId && isset($vendorProduct['variants'])) {
                // Find the specific variant by ID
                foreach ($vendorProduct['variants'] as $variant) {
                    if (isset($variant['id']) && $variant['id'] == $vendorProductVariantId) {
                        $priceWithTax = (float) ($variant['price'] ?? 0);
                        break;
                    }
                }
            }
            // Fallback to first variant if no specific variant found
            if ($priceWithTax == 0 && isset($vendorProduct['variants'][0])) {
                $priceWithTax = (float) ($vendorProduct['variants'][0]['price'] ?? 0);
            }
            
            // Calculate total tax rate from all taxes and collect tax data
            $taxes = $vendorProduct['taxes'] ?? [];
            $taxRate = 0;
            $taxNames = ['en' => [], 'ar' => []];
            $taxesData = []; // Store individual tax data for order_product_taxes
            $processedTaxIds = []; // Track processed tax IDs to avoid duplicates
            
            foreach ($taxes as $tax) {
                $taxId = $tax['id'] ?? null;
                
                // Skip if no tax_id or already processed (avoid duplicates)
                if (!$taxId || in_array($taxId, $processedTaxIds)) {
                    continue;
                }
                $processedTaxIds[] = $taxId;
                
                $taxPercentage = (float) ($tax['percentage'] ?? 0);
                $taxRate += $taxPercentage;
                $taxNames['en'][] = $tax['name_en'] ?? $tax['name'] ?? '';
                $taxNames['ar'][] = $tax['name_ar'] ?? $tax['name'] ?? '';
                
                // Collect tax data for storing in order_product_taxes
                $taxesData[] = [
                    'tax_id' => $taxId,
                    'percentage' => $taxPercentage,
                    'name_en' => $tax['name_en'] ?? $tax['name'] ?? '',
                    'name_ar' => $tax['name_ar'] ?? $tax['name'] ?? '',
                ];
            }
            $taxNameEn = implode(', ', array_filter($taxNames['en']));
            $taxNameAr = implode(', ', array_filter($taxNames['ar']));
            
            $limitation = (int) ($vendorProduct['max_per_order'] ?? 0);

            // Get commission rate from product's department
            $totalCommissionRate = (float) ($vendorProduct['product']['department']['commission'] ?? 0);

            // Calculate price before tax for subtotal calculation
            // If price is 100 with 10% tax, price before tax = 100 / 1.10 = 90.91
            $priceBeforeTax = $taxRate > 0 ? $priceWithTax / (1 + $taxRate / 100) : $priceWithTax;
            
            // Product total with tax (for storing in order_products.price)
            $productTotalWithTax = round($priceWithTax * $quantity, 2);
            
            // Product total before tax (for subtotal calculation)
            $productTotalBeforeTax = round($priceBeforeTax * $quantity, 2);
            
            // Tax amount
            $taxAmount = round($productTotalWithTax - $productTotalBeforeTax, 2);
            
            // Commission is calculated from price WITH tax (15% of total including tax)
            $commissionAmount = round(($productTotalWithTax * $totalCommissionRate) / 100, 2);

            $totalProductPrice += $productTotalBeforeTax;
            $totalTax += $taxAmount;
            $totalCommission += $commissionAmount;
            $itemsCount += $quantity;

            $productsData[] = [
                'vendor_product_id' => $vendorProductId,
                'vendor_product_variant_id' => $vendorProductVariantId,
                'vendor_id' => $vendorId,
                'quantity' => $quantity,
                'price' => $productTotalWithTax, // Store total price INCLUDING tax
                'commission' => $totalCommissionRate, // Store commission percentage only
                'category_id' => $vendorProduct['product']['category']['id'] ?? $vendorProduct['product']['category_id'] ?? null,
                'category_name' => $vendorProduct['product']['category']['name'] ?? null,
                'department_id' => $vendorProduct['product']['department']['id'] ?? $vendorProduct['product']['department_id'] ?? null,
                'department_name' => $vendorProduct['product']['department']['name'] ?? null,
                'sub_category_id' => $vendorProduct['product']['subCategory']['id'] ?? $vendorProduct['product']['sub_category_id'] ?? null,
                'sub_category_name' => $vendorProduct['product']['subCategory']['name'] ?? null,
                'translations' => [
                    'en' => [
                        'name' => $productNameEn,
                    ],
                    'ar' => [
                        'name' => $productNameAr,
                    ],
                ],
                'taxes' => $taxesData, // Array of taxes with their IDs
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'tax_translations' => [
                    'en' => $taxNameEn,
                    'ar' => $taxNameAr,
                ],
                'total' => $productTotalWithTax, // Total includes tax
                'limitation' => $limitation,
            ];

            $productSalesData[$vendorProductId] = $quantity;
        }

        $context['products_data'] = $productsData;
        $context['total_product_price'] = $totalProductPrice; // Subtotal before tax
        $context['total_tax'] = $totalTax;
        $context['total_commission'] = $totalCommission;
        $context['items_count'] = $itemsCount;
        $context['product_sales_to_update'] = $productSalesData;

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
