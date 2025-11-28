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
        return [
            'id' => $this->id,
            'show_end_offer_at_section' => (bool) $this->has_discount,
            'stock' => $this->total_stock ?? 0,
            'sku' => $this->sku,
            'variant_name' => $this->variant_name ?? '',
            // 'configuration' => VariantConfigurationResource::make($this->whenLoaded('variantConfiguration')),
            'real_price' => $this->formatPrice((float) $this->price),
            'fake_price' => $this->price_before_discount ? $this->formatPrice((float) $this->price_before_discount) : null,
            'discount' => $this->discount,
            'quantity_in_cart' => $this->quantity_in_cart,
            'cart_id' => $this->cart_id,
            'countDeliveredProduct' => $this->countDeliveredProduct,
            'countOfAvailable' => $this->countOfAvailable,
            'end_at' => $this->discount_end_date,
            'countDown' => $this->discount_end_date ? OfferExpireDateResource::make($this->getRawOriginal('discount_end_date')) : null,
        ];
    }

    /**
     * Format price with thousand separator
     */
    private function formatPrice(float $price): string
    {
        return number_format($price, 2, '.', '');
    }
}
