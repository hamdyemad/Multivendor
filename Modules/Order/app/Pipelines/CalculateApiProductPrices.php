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

            // Determine price based on type (bundle/occasion have special pricing)
            // Returns null if not found, or the actual price (which could be 0)
            $bundleOccasionPrice = $this->getPriceFromCart($type, $formProduct, $vendorProductVariantId);

            // Use bundle/occasion price if found, otherwise use variant price
            if ($bundleOccasionPrice !== null) {
                $priceWithTax = $bundleOccasionPrice;
            } else {
                // Find the specific variant price, not just the first one
                $priceWithTax = 0;
                if (!empty($vendorProduct['variants'])) {
                    foreach ($vendorProduct['variants'] as $variant) {
                        if (($variant['id'] ?? null) == $vendorProductVariantId) {
                            $priceWithTax = (float) ($variant['price'] ?? 0);
                            break;
                        }
                    }
                    // Fallback to first variant if specific variant not found
                    if (!$priceWithTax) {
                        $priceWithTax = (float) ($vendorProduct['variants'][0]['price'] ?? 0);
                    }
                }
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
     * Falls back to database query if cart data doesn't have the products
     * Returns null if not found, or the actual price (which could be 0)
     */
    private function getPriceFromCart(string $type, array $formProduct, $vendorProductVariantId): ?float
    {
        $variantId = (int) $vendorProductVariantId;
        
        if ($type === 'bundle' && !empty($formProduct['bundle_id'])) {
            $bundleId = (int) $formProduct['bundle_id'];
            
            // First try to get from cart data
            if (!empty($formProduct['bundle']['bundleProducts'])) {
                foreach ($formProduct['bundle']['bundleProducts'] as $bundleProduct) {
                    if ((int) ($bundleProduct['vendor_product_variant_id'] ?? 0) === $variantId) {
                        if (array_key_exists('price', $bundleProduct)) {
                            return (float) $bundleProduct['price'];
                        }
                    }
                }
            }

            // Fallback: query database directly (without global scopes to ensure we get the data)
            $bundleProduct = \Modules\CatalogManagement\app\Models\BundleProduct::withoutGlobalScopes()
                ->where('bundle_id', $bundleId)
                ->where('vendor_product_variant_id', $variantId)
                ->whereNull('deleted_at')
                ->first();

            if ($bundleProduct) {
                return (float) $bundleProduct->price;
            }
        }

        if ($type === 'occasion' && !empty($formProduct['occasion_id'])) {
            $occasionId = (int) $formProduct['occasion_id'];
            
            // First try to get from cart data
            if (!empty($formProduct['occasion']['occasionProducts'])) {
                foreach ($formProduct['occasion']['occasionProducts'] as $occasionProduct) {
                    if ((int) ($occasionProduct['vendor_product_variant_id'] ?? 0) === $variantId) {
                        if (array_key_exists('special_price', $occasionProduct)) {
                            return (float) $occasionProduct['special_price'];
                        }
                    }
                }
            }

            // Fallback: query database directly
            $occasionProduct = \Modules\CatalogManagement\app\Models\OccasionProduct::query()
                ->where('occasion_id', $occasionId)
                ->where('vendor_product_variant_id', $variantId)
                ->first();

            if ($occasionProduct) {
                return (float) $occasionProduct->special_price;
            }
        }

        // Return null to indicate price not found - will use regular product price
        return null;
    }
}
