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

        // Calculate points based on price
        $price = (float) ($this->price ?? 0);
        $points = PointsHelper::calculatePoints($price);

        // Calculate price after taxes
        $priceBeforeTaxes = (float) ($this->price ?? 0);
        $fakePriceBeforeTaxes = $this->price_before_discount ? (float) $this->price_before_discount : null;
        $priceAfterTaxes = $priceBeforeTaxes;
        $fakePriceAfterTaxes = $fakePriceBeforeTaxes;
        
        // Load taxes if not already loaded
        $vendorProduct = $this->vendorProduct;
        if ($vendorProduct && !$vendorProduct->relationLoaded('taxes')) {
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
            'variant_price_before_taxes' => round($priceBeforeTaxes, 2),
            'variant_real_price' => round($priceAfterTaxes, 2),
            'variant_fake_price' => $fakePriceAfterTaxes ? round($fakePriceAfterTaxes, 2) : null,
            'variant_discount' => $this->discount ?? 0,
            'configuration_tree' => $this->when($this->relationLoaded('variantConfiguration') && $this->variantConfiguration, function() use ($locale) {
                return $this->buildVariantConfigurationTree($this->variantConfiguration, $this->id, $locale);
            }),
            'price_before_taxes' => round($priceBeforeTaxes, 2),
            'real_price' => round($priceAfterTaxes, 2),
            'fake_price' => $fakePriceAfterTaxes ? round($fakePriceAfterTaxes, 2) : null,
            'discount' => $this->discount ?? 0,
            'countDeliveredProduct' => $this->countDeliveredProduct ?? 0,
            'countOfAvailable' => $this->countOfAvailable ?? 0,
        ];
    }
}
