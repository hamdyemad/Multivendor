<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\PointsHelper;

class CartProductResource extends JsonResource
{
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
            'real_price' => round((float) ($this->price ?? 0), 2),
            'fake_price' => $this->price_before_discount ? round((float) $this->price_before_discount, 2) : null,
            'discount' => $this->discount ?? 0,
            'countDeliveredProduct' => $this->countDeliveredProduct ?? 0,
            'countOfAvailable' => $this->countOfAvailable ?? 0,
        ];
    }
}