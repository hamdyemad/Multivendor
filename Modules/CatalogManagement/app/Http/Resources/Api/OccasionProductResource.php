<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Vendor\app\Http\Resources\Api\LightVendorResource;
use App\Helpers\PointsHelper;

class OccasionProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $variant = $this->vendorProductVariant;
        $vendorProduct = $variant?->vendorProduct;
        $product = $vendorProduct?->product;
        $vendor = $vendorProduct?->vendor;
        $brand = $product?->brand;
        $locale = app()->getLocale();

        if (!$vendorProduct || !$product) {
            return [];
        }

        // Get reviews data
        $totalReviews = $vendorProduct->reviews_count ?? $vendorProduct->reviews()->count();
        $avgStar = $vendorProduct->reviews_avg_star ?? $vendorProduct->reviews()->avg('star') ?? 0;

        // Calculate points based on price
        $price = $this->special_price ?? $variant?->price ?? 0;
        $points = PointsHelper::calculatePoints((float) $price);

        // Build variants array with the product nested inside
        $variants = [];
        if ($variant) {
            $variantData = $this->buildVariantData($variant, $vendorProduct, $product, $vendor, $brand, $locale);
            $variants[] = $variantData;
        }

        return [
            'id' => $vendorProduct->id,
            'show_end_offer_at_section' => (bool) ($variant?->has_discount ?? false),
            'image' => formatImage($product->mainImage),
            'name' => $product->getTranslation('title', $locale) ?? $product->title,
            'slug' => $product->slug,
            'points' => $points,
            'sku' => $vendorProduct->sku ?? $variant?->sku,
            'details' => $product->getTranslation('details', $locale) ?? '',
            'summary' => $product->getTranslation('summary', $locale) ?? '',
            'instructions' => $product->getTranslation('instructions', $locale) ?? '',
            'features' => $product->getTranslation('features', $locale) ?? '',
            'extras' => $product->getTranslation('extra_description', $locale) ?? '',
            'star' => round($avgStar, 1),
            'num_of_user_review' => $totalReviews,
            'number_of_sale' => $product->sales ?? 0,
            'video_link' => $product->video_link ?? '',
            'stock' => $vendorProduct->total_stock ?? 0,
            'views' => $product->views ?? 0,
            'matrial' => $product->getTranslation('material', $locale) ?? '',
            'status' => $vendorProduct->is_active ? 'Active' : 'Inactive',
            'limitation' => $vendorProduct->max_per_order ?? 10,
            'is_fav' => $vendorProduct->is_fav ?? false,
            'size_color_type' => $this->getSizeColorType($product),
            'special_price' => $this->special_price ? round($this->special_price, 2) : null,
            'vendor' => $vendor ? new LightVendorResource($vendor) : null,
            'brand' => $brand ? $this->formatBrand($brand, $locale) : null,
            'variants' => $variants,
            'tags' => $product->tags_array ?? [],
            'meta_description' => $product->getTranslation('meta_description', $locale) ?? '',
            'meta_keywords' => $product->meta_keywords ?? [],
        ];
    }

    /**
     * Build variant data (product data is at top level, not nested in variants)
     */
    private function buildVariantData($variant, $vendorProduct, $product, $vendor, $brand, $locale): array
    {
        // Build configuration with tree structure
        $configuration = null;
        if ($variant->variantConfiguration) {
            $configuration = $this->buildConfigurationTree($variant->variantConfiguration, $locale);
        }

        // Calculate countdown
        $countDown = null;
        if ($variant->discount_end_date) {
            $endDate = \Carbon\Carbon::parse($variant->discount_end_date);
            $now = now();
            $diff = $now->diff($endDate);
            
            $countDown = [
                'product_id' => $vendorProduct->id,
                'days' => $diff->invert ? 0 : $diff->days,
                'hours' => $diff->invert ? 0 : $diff->h,
                'minutes' => $diff->invert ? 0 : $diff->i,
                'seconds' => $diff->invert ? 0 : $diff->s,
            ];
        }

        return [
            'id' => $variant->id,
            'show_end_offer_at_section' => (bool) $variant->has_discount,
            'stock' => $variant->total_stock ?? 0,
            'sku' => $variant->sku,
            'variant_name' => $variant->{"variant_path_{$locale}"} ?? '',
            'configuration' => $configuration,
            'real_price' => round(($this->special_price ?? $variant->price), 2),
            'fake_price' => $variant->price_before_discount ? round($variant->price_before_discount, 2) : null,
            'discount' => $variant->discount,
            'special_price' => $this->special_price ? round($this->special_price, 2) : null,
            'quantity_in_cart' => $variant->quantity_in_cart ?? null,
            'cart_id' => $variant->cart_id ?? null,
            'countDeliveredProduct' => $variant->fulfilled_stock ?? 0,
            'countOfAvailable' => $variant->remaining_stock ?? $variant->total_stock ?? 0,
            'end_at' => $variant->discount_end_date ? \Carbon\Carbon::parse($variant->discount_end_date)->format('Y-m-d') : null,
            'countDown' => $countDown,
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
        
        // Add parent if exists
        if ($configuration->parent_id && $configuration->relationLoaded('parent_data') && $configuration->parent_data) {
            $configData['parent'] = $this->buildConfigurationTree($configuration->parent_data, $locale);
        }
        
        return $configData;
    }

    /**
     * Format brand data
     */
    private function formatBrand($brand, $locale): array
    {
        return [
            'id' => $brand->id,
            'name' => $brand->getTranslation('name', $locale) ?? $brand->name,
            'slug' => $brand->slug,
            'image' => formatImage($brand->image),
        ];
    }

    /**
     * Get size/color type based on configuration
     */
    private function getSizeColorType($product): string
    {
        if ($product->configuration_type === 'simple') {
            return 'without_any';
        }
        return 'with_variants';
    }
}
