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
            'vendor_name' => $this->vendorProduct && $this->vendorProduct->vendor ? 
                $this->vendorProduct->vendor->name : 'N/A',
            'vendor_product' => $this->whenLoaded('vendorProduct', function() {
                return new VendorProductResource($this->vendorProduct);
            }),
            'real_price' => $this->formatPrice((float) $this->price),
            'fake_price' => $this->price_before_discount ? $this->formatPrice((float) $this->price_before_discount) : null,
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
     * Format price with thousand separator
     */
    private function formatPrice(float $price): string
    {
        return number_format($price, 2, '.', '');
    }
}
