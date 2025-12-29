<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use App\Exceptions\OrderException;
use Modules\CatalogManagement\app\Services\Api\ProductApiService;

class CalculateApiProductPrices
{
    public function __construct(
        private ProductApiService $productService,
    ) {}

    /**
     * Handle the pipeline for API checkout.
     *
     * Fetches complete product data from service and prepares data for OrderProduct table.
     * Handles product, bundle, and occasion types with appropriate pricing.
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
            $vendorProductId = $formProduct['vendor_product_id'];
            $vendorProductVariantId = $formProduct['vendor_product_variant_id'] ?? null;
            $quantity = (int) $formProduct['quantity'];
            $type = $formProduct['type'] ?? 'product';
            $bundleId = $formProduct['bundle_id'] ?? null;
            $occasionId = $formProduct['occasion_id'] ?? null;

            // Determine price based on type FIRST (using cart data which has occasion/bundle pricing)
            $priceWithTax = $this->getPriceFromCart($type, $formProduct);

            // Get product details from service with all relationships for order creation
            $vendorProduct = $this->productService->findProductForOrder($vendorProductId);

            // Validate product data exists
            if (!$vendorProduct || !isset($vendorProduct['product'])) {
                throw new OrderException(trans('order::order.product_not_found', ['id' => $vendorProductId]));
            }

            // Extract necessary data
            $productId = $vendorProduct['product']['id'] ?? null;
            if (!$productId) {
                throw new OrderException(trans('order::order.product_id_not_found', ['id' => $vendorProductId]));
            }

            $productNameEn = $vendorProduct['product']['title_en'] ?? $vendorProduct['product']['title'] ?? 'Unknown Product';
            $productNameAr = $vendorProduct['product']['title_ar'] ?? $vendorProduct['product']['title'] ?? 'Unknown Product';
            $vendorId = $vendorProduct['vendor']['id'] ?? null;
            if (!$vendorId) {
                throw new OrderException(trans('order::order.vendor_id_not_found', ['id' => $vendorProductId]));
            }

            // If price wasn't determined from cart, use variant price as fallback
            if (!$priceWithTax) {
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
                'commission' => $commissionAmount, // Commission calculated from price with tax
                'type' => $type,
                'bundle_id' => $bundleId,
                'occasion_id' => $occasionId,
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

    /**
     * Get price from cart data (occasion/bundle pricing)
     * Returns 0 if not found, allowing fallback to variant price
     */
    private function getPriceFromCart(string $type, array $formProduct): float
    {
        if ($type === 'bundle' && isset($formProduct['bundle'])) {
            $bundle = $formProduct['bundle'];
            if ($bundle && isset($bundle['bundleProducts'])) {
                $bundleProduct = collect($bundle['bundleProducts'])
                    ->firstWhere('vendor_product_variant_id', $formProduct['vendor_product_variant_id']);

                if ($bundleProduct && isset($bundleProduct['price'])) {
                    return (float) $bundleProduct['price'];
                }
            }
        }

        if ($type === 'occasion' && isset($formProduct['occasion'])) {
            $occasion = $formProduct['occasion'];
            if ($occasion && isset($occasion['occasionProducts'])) {
                $occasionProduct = collect($occasion['occasionProducts'])
                    ->firstWhere('vendor_product_variant_id', $formProduct['vendor_product_variant_id']);

                if ($occasionProduct && isset($occasionProduct['special_price'])) {
                    return (float) $occasionProduct['special_price'];
                }
            }
        }

        // Return 0 to indicate price not found in cart data
        return 0;
    }
}
