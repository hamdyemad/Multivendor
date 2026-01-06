<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        
        // Build configuration object with tree structure
        $configuration = null;
        if ($this->variantConfiguration) {
            $configuration = $this->buildConfigurationTree($this->variantConfiguration, $locale);
        }

        // Calculate price after taxes
        $priceBeforeTaxes = (float) $this->price;
        $fakePriceBeforeTaxes = $this->price_before_discount ? (float) $this->price_before_discount : null;
        $priceAfterTaxes = $priceBeforeTaxes;
        $fakePriceAfterTaxes = $fakePriceBeforeTaxes;
        
        // Get taxes from vendor product - load if not already loaded
        $vendorProduct = $this->vendorProduct;
        if (!$vendorProduct && $this->vendor_product_id) {
            $vendorProduct = \Modules\CatalogManagement\app\Models\VendorProduct::with('taxes')->find($this->vendor_product_id);
        } elseif ($vendorProduct && !$vendorProduct->relationLoaded('taxes')) {
            $vendorProduct->load('taxes');
        }
        
        if ($vendorProduct && $vendorProduct->taxes && $vendorProduct->taxes->count() > 0) {
            $totalTaxPercentage = $vendorProduct->taxes->sum('percentage');
            $taxMultiplier = 1 + ($totalTaxPercentage / 100);
            $priceAfterTaxes = $priceBeforeTaxes * $taxMultiplier;
            if ($fakePriceBeforeTaxes) {
                $fakePriceAfterTaxes = $fakePriceBeforeTaxes * $taxMultiplier;
            }
        }
        
        return [
            'id' => $this->id,
            'show_end_offer_at_section' => (bool) $this->has_discount,
            'stock' => $this->total_stock ?? 0,
            'booked_stock' => $this->booked_stock ?? 0,
            'allocated_stock' => $this->allocated_stock ?? 0,
            'fulfilled_stock' => $this->fulfilled_stock ?? 0,
            'remaining_stock' => $this->remaining_stock ?? 0,
            'sku' => $this->sku,
            'variant_name' => $this->{"variant_path_{$locale}"} ?? '',
            'variant_key' => $this->variantConfiguration && $this->variantConfiguration->key ? 
                ($this->variantConfiguration->key->getTranslation('name', $locale) ?? $this->variantConfiguration->key->name) : '',
            'variant_value' => $this->variantConfiguration ? 
                ($this->variantConfiguration->getTranslation('name', $locale) ?? ($this->variantConfiguration->name ?? $this->variantConfiguration->value)) : '',
            'configuration' => $configuration,
            'vendor_name' => $vendorProduct ? 
                ($vendorProduct->relationLoaded('vendor') && $vendorProduct->vendor ? $vendorProduct->vendor->name : null) : null,
            'price_before_taxes' => $this->formatPrice($priceBeforeTaxes),
            'real_price' => $this->formatPrice($priceAfterTaxes),
            'fake_price' => $fakePriceAfterTaxes ? $this->formatPrice($fakePriceAfterTaxes) : null,
            'discount' => $this->discount,
            'quantity_in_cart' => $this->quantity_in_cart,
            'cart_id' => $this->cart_id,
            'countDeliveredProduct' => $this->countDeliveredProduct,
            'countOfAvailable' => $this->countOfAvailable,
            'end_at' => $this->discount_end_at,
            'countDown' => $this->discount_end_date ? OfferExpireDateResource::make($this->getRawOriginal('discount_end_date')) : null,
        ];
    }

    /**
     * Build configuration tree recursively
     */
    private function buildConfigurationTree($configuration, $locale): array
    {
        // Get color value - only if type is 'color', use the value field
        $colorValue = null;
        if ($configuration->type === 'color') {
            $colorValue = $configuration->value;
        }
        
        $configData = [
            'id' => $configuration->id,
            'name' => $configuration->getTranslation('name', $locale) ?? $configuration->name ?? $configuration->value,
            'color' => $colorValue,
            'key' => $configuration->key ? [
                'id' => $configuration->key->id,
                'name' => $configuration->key->getTranslation('name', $locale) ?? $configuration->key->name,
            ] : null,
        ];
        
        // Add parent if exists (going up the tree)
        if ($configuration->parent_id && $configuration->relationLoaded('parent_data') && $configuration->parent_data) {
            $configData['parent'] = $this->buildConfigurationTree($configuration->parent_data, $locale);
        }
        
        // Add children if exists (going down the tree)
        if ($configuration->relationLoaded('childrenRecursive') && $configuration->childrenRecursive && $configuration->childrenRecursive->count() > 0) {
            $configData['children'] = $configuration->childrenRecursive->map(function ($child) use ($locale) {
                return $this->buildConfigurationTree($child, $locale);
            })->toArray();
        }
        
        return $configData;
    }

    /**
     * Format price with thousand separator
     */
    private function formatPrice(float $price): string
    {
        return number_format($price, 2, '.', '');
    }
    
    /**
     * Build full key tree with only product-related variants and mark selected
     */
    private function buildFullKeyTree($configuration, $locale): ?array
    {
        if (!$configuration || !$configuration->key) {
            return null;
        }
        
        $key = $configuration->key;
        $selectedId = $configuration->id;
        $selectedPath = $this->getSelectedPath($configuration);
        
        // Get variant IDs that belong to this product
        $vendorProduct = $this->vendorProduct;
        $productVariantConfigIds = [];
        
        if ($vendorProduct && $vendorProduct->relationLoaded('variants')) {
            $productVariantConfigIds = $vendorProduct->variants
                ->pluck('variant_configuration_id')
                ->filter()
                ->toArray();
        }
        
        // Load only variants under this key that are used by this product
        $variants = \Modules\CatalogManagement\app\Models\VariantsConfiguration::where('key_id', $key->id)
            ->whereNull('parent_id')
            ->whereIn('id', $productVariantConfigIds)
            ->with(['childrenRecursive', 'childrenRecursive.key'])
            ->get();
        
        return [
            'id' => $key->id,
            'name' => $key->getTranslation('name', $locale) ?? $key->name,
            'type' => 'key',
            'selected_variant_id' => $selectedId,
            'selected_path' => $selectedPath,
            'children' => $variants->map(function ($variant) use ($locale, $selectedId, $selectedPath) {
                return $this->buildVariantNode($variant, $locale, $selectedId, $selectedPath);
            })->toArray(),
        ];
    }
    
    /**
     * Build variant node recursively
     */
    private function buildVariantNode($variant, $locale, $selectedId, $selectedPath): array
    {
        $isSelected = $variant->id === $selectedId || in_array($variant->id, $selectedPath);
        
        $children = [];
        if ($variant->relationLoaded('childrenRecursive') && $variant->childrenRecursive->count() > 0) {
            $children = $variant->childrenRecursive->map(function ($child) use ($locale, $selectedId, $selectedPath) {
                return $this->buildVariantNode($child, $locale, $selectedId, $selectedPath);
            })->toArray();
        }
        
        return [
            'id' => $variant->id,
            'name' => $variant->getTranslation('name', $locale) ?? $variant->name ?? $variant->value,
            'value' => $variant->value,
            'type' => $variant->type,
            'color' => $variant->type === 'color' ? $variant->value : null,
            'key_id' => $variant->key_id,
            'parent_id' => $variant->parent_id,
            'is_selected' => $isSelected,
            'has_children' => count($children) > 0,
            'children_count' => count($children),
            'children' => $children,
        ];
    }
    
    /**
     * Get selected path (array of IDs from root to selected)
     */
    private function getSelectedPath($configuration): array
    {
        $path = [$configuration->id];
        
        // Walk up the tree to get parent IDs
        $current = $configuration;
        while ($current->parent_id && $current->relationLoaded('parent_data') && $current->parent_data) {
            array_unshift($path, $current->parent_data->id);
            $current = $current->parent_data;
        }
        
        return $path;
    }
}
