<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'show_end_offer_at_section' => true,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'variant_name' => $this->getVariantName(),
            'configuration' => VariantConfigurationResource::make($this->whenLoaded('variant')),
            'real_price' => $this->formatPrice($this->price),
            'fake_price' => $this->discount_percentage ? $this->formatPrice($this->price_before_discount ?? $this->price) : null,
            'discount' => $this->discount_percentage,
            'quantity_in_cart' => null,
            'cart_id' => null,
            'countDeliveredProduct' => 0,
            'countOfAvailable' => $this->stock,
            'end_at' => $this->discount_end_date,
            'countDown' => $this->discount_end_date ? new OfferExpireDateResource($this) : null,
        ];
    }

    /**
     * Get variant name from configuration
     */
    private function getVariantName(): string
    {
        if ($this->relationLoaded('variant') && $this->variant) {
            return $this->variant->getTranslation('value', app()->getLocale()) ?? '';
        }
        return '';
    }

    /**
     * Format price with thousand separator
     */
    private function formatPrice($price): string
    {
        return number_format((float) $price, 2);
    }
}
