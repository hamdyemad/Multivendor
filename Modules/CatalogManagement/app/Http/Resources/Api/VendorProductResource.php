<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CategoryManagment\app\Http\Resources\Api\GeneralResoruce;
use Modules\CategoryManagment\app\Http\Resources\Api\LightCategoryApiResource;
use Modules\CategoryManagment\app\Http\Resources\Api\LightDepartmentApiResource;
use Modules\CategoryManagment\app\Http\Resources\Api\LightSubCategoryApiResource;
use Modules\Vendor\app\Http\Resources\Api\LightVendorResource;
use Modules\CatalogManagement\app\Http\Resources\Api\VendorProductVariantResource;
use App\Helpers\PointsHelper;

class VendorProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Use pre-loaded counts from withCount/withAvg
        $totalReviews = $this->reviews_count ?? 0;
        $avgStar = $this->reviews_avg_star ?? 0;

        // Calculate points based on minimum variant price
        $price = $this->variants?->min('price') ?? 0;
        $points = PointsHelper::calculatePoints((float) $price);

        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'product_id' => $this->product_id,
            'slug' => $this->product?->slug,
            'points' => $points,
            'sku' => $this->sku,
            'reviews_count' => $totalReviews,
            'review_avg_star' => round($avgStar, 2),
            'limitation' => $this->max_per_order,
            'status' => $this->is_featured ? __('catalogmanagement::product.featured') : __('catalogmanagement::product.active'),
            'image' => $this->whenLoaded('product', function() {
                return formatImage($this->product->mainImage);
            }),
            'images' => $this->whenLoaded('product', function() {
                return $this->product->additionalImages?->map(fn($img) => formatImage($img))->filter()->values() ?? [];
            }),
            'name' => $this->product?->title,
            'details' => $this->product?->details,
            'summary' => $this->product?->summary,
            'instructions' => $this->product?->instructions,
            'features' => $this->product?->features,
            'extras' => $this->product?->extra_description,
            'matrial' => $this->product?->material,
            'video_link' => $this->product?->video_link,

            'number_of_sale' => $this->sales,
            'views' => $this->views,
            'sort_number' => $this->sort_number ?? 0,
            'stock' => $this->total_stock ?? 0,
            'booked_stock' => $this->booked_stock ?? 0,
            'allocated_stock' => $this->allocated_stock ?? 0,
            'delivered_stock' => $this->fulfilled_stock ?? 0,
            'remaining_stock' => $this->remaining_stock ?? 0,

            'is_fav' => $this->is_fav,
            'configuration_type' => $this->product?->configuration_type,
            'tags' => $this->product?->tags_array ?? [],
            'meta_description' => $this->product?->meta_description,
            'meta_keywords' => $this->product?->meta_keywords ?? [],
            'vendor' => LightVendorResource::make($this->whenLoaded('vendor')),
            'brand' => $this->whenLoaded('product', function() {
                return $this->product->brand ? GeneralResoruce::make($this->product->brand) : null;
            }),
            'taxes' => TaxResource::collection($this->whenLoaded('taxes')),
            'variants' => $this->when($this->relationLoaded('variants'), function() {
                // Set vendorProduct relation on each variant so it can access taxes
                // Sort by price descending (highest price first)
                $variants = $this->variants->sortByDesc('price')->values();
                foreach ($variants as $variant) {
                    $variant->setRelation('vendorProduct', $this->resource);
                }
                return VendorProductVariantResource::collection($variants);
            }),
            'configuration_tree' => $this->when($this->relationLoaded('variants'), function() {
                return $this->buildConfigurationTree();
            }),
            'department' => $this->when($this->relationLoaded('product') && $this->product?->relationLoaded('department'), function() {
                return LightDepartmentApiResource::make($this->product->department);
            }),
            'category' => $this->when($this->relationLoaded('product') && $this->product?->relationLoaded('category'), function() {
                return LightCategoryApiResource::make($this->product->category);
            }),
            'sub_category' => $this->when($this->relationLoaded('product') && $this->product?->relationLoaded('subCategory'), function() {
                return LightSubCategoryApiResource::make($this->product->subCategory);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
    
    /**
     * Build configuration tree with all keys and their variants for this product
     */
    private function buildConfigurationTree(): array
    {
        $locale = app()->getLocale();
        $variants = $this->variants;
        
        if ($variants->isEmpty()) {
            return [];
        }
        
        // Use taxes for price calculation
        $taxes = $this->taxes ?? collect();
        $totalTaxPercentage = $taxes->sum('percentage');
        $taxMultiplier = 1 + ($totalTaxPercentage / 100);

        // Handle simple products
        if ($this->product?->configuration_type === 'simple') {
            $variant = $variants->first();
            
            $priceBeforeTax = (float) ($variant->price ?? 0);
            $priceAfterTax = $priceBeforeTax * $taxMultiplier;
            $fakePriceBeforeTax = $variant->price_before_discount ? (float) $variant->price_before_discount : null;
            $fakePriceAfterTax = $fakePriceBeforeTax ? $fakePriceBeforeTax * $taxMultiplier : null;

            return [[
                'id' => 0,
                'name' => $this->product->title ?? __('catalogmanagement::product.simple'),
                'type' => 'simple',
                'children' => [[
                    'id' => 0,
                    'name' => $this->product->title ?? __('catalogmanagement::product.simple'),
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
                        'quantity_in_cart' => $variant->quantity_in_cart,
                        'cart_id' => $variant->cart_id,
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
        $configIds = array_keys($variantMap);
        
        // Use already loaded variant configurations to avoid N+1 query
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
                    'quantity_in_cart' => $variant->quantity_in_cart,
                    'cart_id' => $variant->cart_id,
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
