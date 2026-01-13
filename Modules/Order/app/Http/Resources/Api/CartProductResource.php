<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\PointsHelper;
use Modules\Order\app\Traits\HasVariantConfigurationTree;

class CartProductResource extends JsonResource
{
    use HasVariantConfigurationTree;
    
    /**
     * Cart context passed from CartResource
     */
    public ?array $cartContext = null;
    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        
        // Add null safety checks
        if (!$this->vendorProduct || !$this->vendorProduct->product) {
            return [];
        }

        $product = $this->vendorProduct->product;

        // Get cart context if available (bundle/occasion price)
        $cartType = $this->cartContext['type'] ?? 'product';
        $bundlePrice = $this->cartContext['bundle_price'] ?? null;
        $occasionPrice = $this->cartContext['occasion_price'] ?? null;

        // Original variant price (always the real product price)
        $originalVariantPrice = (float) ($this->price ?? 0);
        
        // Variant price (bundle/occasion price if applicable, otherwise original)
        if ($cartType === 'bundle' && $bundlePrice !== null) {
            $variantPrice = (float) $bundlePrice;
        } elseif ($cartType === 'occasion' && $occasionPrice !== null) {
            $variantPrice = (float) $occasionPrice;
        } else {
            $variantPrice = $originalVariantPrice;
        }

        // Calculate points based on variant price
        $points = PointsHelper::calculatePoints($variantPrice);

        // Calculate prices after taxes
        $variantPriceAfterTaxes = $variantPrice;
        
        // Load taxes if not already loaded
        $vendorProduct = $this->vendorProduct;
        if ($vendorProduct && !$vendorProduct->relationLoaded('taxes')) {
            $vendorProduct->load('taxes');
        }
        
        if ($vendorProduct && $vendorProduct->taxes && $vendorProduct->taxes->count() > 0) {
            $totalTaxPercentage = $vendorProduct->taxes->sum('percentage');
            $taxMultiplier = 1 + ($totalTaxPercentage / 100);
            $variantPriceAfterTaxes = $variantPrice * $taxMultiplier;
        }

        // Calculate discount percentage
        $discount = 0;
        if ($originalVariantPrice > $variantPrice) {
            $discount = round((($originalVariantPrice - $variantPrice) / $originalVariantPrice) * 100);
        }

        return [
            'id' => $product->id,
            'image' => formatImage($product->mainImage),
            'name' => $product->title,
            'slug' => $product->slug,
            'points' => $points,
            'status' => $this->vendorProduct->is_featured ? __('catalogmanagement::product.featured') : __('catalogmanagement::product.active'),
            'is_fav' => false,
            'star' => $this->vendorProduct->average_rating ?? 0,
            'num_of_user_review' => $this->vendorProduct->reviews_count ?? 0,
            'number_of_sale' => $this->vendorProduct->sales ?? 0,
            'stock' => $this->total_stock ?? 0,
            'sku' => $this->sku ?? null,
            'variant_id' => $this->id,
            'variant_name' => $this->{"variant_path_{$locale}"} ?? '',
            'variant_sku' => $this->sku ?? null,
            'variant_stock' => $this->total_stock ?? 0,
            'variant_remaining_stock' => $this->remaining_stock ?? 0,
            // Variant prices (bundle/occasion price if applicable)
            'variant_price_before_taxes' => round($variantPrice, 2),
            'variant_tax_amount' => round($variantPriceAfterTaxes - $variantPrice, 2),
            'variant_price_after_taxes' => round($variantPriceAfterTaxes, 2),
            'configuration_tree' => $this->when($this->relationLoaded('variantConfiguration') && $this->variantConfiguration, function() use ($locale) {
                return $this->buildVariantConfigurationTree($this->variantConfiguration, $this->id, $locale);
            }),
            'countDeliveredProduct' => $this->countDeliveredProduct ?? 0,
            'countOfAvailable' => $this->countOfAvailable ?? 0,
        ];
    }
}
