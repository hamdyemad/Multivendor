<?php

namespace Modules\Refund\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RefundRequestItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        
        return [
            'id' => $this->id,
            'refund_request_id' => $this->refund_request_id,
            'order_product_id' => $this->order_product_id,
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'total_price' => (float) $this->total_price,
            'shipping_amount' => (float) $this->shipping_amount,
            'tax_amount' => (float) $this->tax_amount,
            'discount_amount' => (float) $this->discount_amount,
            'refund_amount' => (float) $this->refund_amount,
            'reason' => $this->reason,
            
            // Product details with configuration tree
            'product' => $this->whenLoaded('orderProduct', function () use ($locale) {
                $vendorProduct = $this->orderProduct?->vendorProduct;
                $product = $vendorProduct?->product;
                
                if (!$product) {
                    return null;
                }
                
                return [
                    'id' => $product->id,
                    'name' => $product->title,
                    'slug' => $product->slug,
                    'image' => formatImage($product->mainImage),
                    'configuration_tree' => $this->buildProductConfigurationTree($vendorProduct, $locale),
                ];
            }),
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
    
    /**
     * Build configuration tree for the product with all variants
     * Similar to VendorProductResource::buildConfigurationTree()
     */
    private function buildProductConfigurationTree($vendorProduct, $locale): array
    {
        if (!$vendorProduct || !$vendorProduct->relationLoaded('variants')) {
            return [];
        }
        
        $variants = $vendorProduct->variants;
        
        if ($variants->isEmpty()) {
            return [];
        }
        
        // Get taxes for price calculation
        $taxes = $vendorProduct->taxes ?? collect();
        $totalTaxPercentage = $taxes->sum('percentage');
        $taxMultiplier = 1 + ($totalTaxPercentage / 100);
        
        // Handle simple products
        if ($vendorProduct->product?->configuration_type === 'simple') {
            $variant = $variants->first();
            
            $priceBeforeTax = (float) ($variant->price ?? 0);
            $priceAfterTax = $priceBeforeTax * $taxMultiplier;
            $fakePriceBeforeTax = $variant->price_before_discount ? (float) $variant->price_before_discount : null;
            $fakePriceAfterTax = $fakePriceBeforeTax ? $fakePriceBeforeTax * $taxMultiplier : null;

            return [[
                'id' => 0,
                'name' => $vendorProduct->product->title ?? 'Simple Product',
                'type' => 'simple',
                'children' => [[
                    'id' => 0,
                    'name' => $vendorProduct->product->title ?? 'Simple Product',
                    'value' => null,
                    'type' => 'simple',
                    'key_id' => 0,
                    'parent_id' => null,
                    'variant' => [
                        'id' => $variant->id,
                        'sku' => $variant->sku,
                        'stock' => $variant->total_stock ?? 0,
                        'remaining_stock' => $variant->remaining_stock ?? 0,
                        'price_before_taxes' => number_format($priceBeforeTax, 2, '.', ''),
                        'real_price' => number_format($priceAfterTax, 2, '.', ''),
                        'fake_price' => $fakePriceAfterTax ? number_format($fakePriceAfterTax, 2, '.', '') : null,
                        'discount' => $variant->discount,
                        'quantity_in_cart' => $variant->quantity_in_cart ?? 0,
                        'cart_id' => $variant->cart_id ?? null,
                    ]
                ]]
            ]];
        }
        
        // Build a map of configuration_id => variant data
        $variantMap = [];
        foreach ($variants as $variant) {
            if ($variant->variant_configuration_id) {
                $variantMap[$variant->variant_configuration_id] = $variant;
            }
        }
        
        if (empty($variantMap)) {
            return [];
        }
        
        // Get all unique configuration IDs from variants
        $configurations = $variants->pluck('variantConfiguration')->filter()->unique('id');
        
        // Group by key
        $keyGroups = [];
        foreach ($configurations as $config) {
            $key = $config->key;
            if (!$key) continue;
            
            if (!isset($keyGroups[$key->id])) {
                $keyGroups[$key->id] = [
                    'id' => $key->id,
                    'name' => $key->getTranslation('name', $locale) ?? $key->name,
                    'type' => 'key',
                    'children' => [],
                ];
            }
            
            // Get variant data for this configuration
            $variant = $variantMap[$config->id] ?? null;
            $priceBeforeTax = $variant ? (float) $variant->price : 0;
            $priceAfterTax = $priceBeforeTax * $taxMultiplier;
            $fakePriceBeforeTax = $variant && $variant->price_before_discount ? (float) $variant->price_before_discount : null;
            $fakePriceAfterTax = $fakePriceBeforeTax ? $fakePriceBeforeTax * $taxMultiplier : null;
            
            $keyGroups[$key->id]['children'][] = [
                'id' => $config->id,
                'name' => $config->getTranslation('name', $locale) ?? $config->name ?? $config->value,
                'value' => $config->value,
                'type' => $config->type,
                'color' => $config->type === 'color' ? $config->value : null,
                'key_id' => $config->key_id,
                'parent_id' => $config->parent_id,
                'variant' => $variant ? [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'stock' => $variant->total_stock ?? 0,
                    'remaining_stock' => $variant->remaining_stock ?? 0,
                    'price_before_taxes' => number_format($priceBeforeTax, 2, '.', ''),
                    'real_price' => number_format($priceAfterTax, 2, '.', ''),
                    'fake_price' => $fakePriceAfterTax ? number_format($fakePriceAfterTax, 2, '.', '') : null,
                    'discount' => $variant->discount,
                    'quantity_in_cart' => $variant->quantity_in_cart ?? 0,
                    'cart_id' => $variant->cart_id ?? null,
                ] : null,
                '_price' => $priceBeforeTax, // for sorting
            ];
        }
        
        // Sort children by price descending (highest first)
        foreach ($keyGroups as &$group) {
            usort($group['children'], function ($a, $b) {
                return ($b['_price'] ?? 0) <=> ($a['_price'] ?? 0);
            });
            // Remove _price helper field
            $group['children'] = array_map(function ($child) {
                unset($child['_price']);
                return $child;
            }, $group['children']);
        }
        
        return array_values($keyGroups);
    }
}
