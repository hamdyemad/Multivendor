<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
            'order_number' => $this->order_number,
            'customer' => [
                'name' => $this->customer_name,
                'email' => $this->customer_email,
                'phone' => $this->customer_phone,
                'address' => $this->customer_address,
            ],
            'location' => [
                'country_name' => $this->country?->name,
                'city_name' => $this->city?->name,
                'region_name' => $this->region?->name,
            ],
            'order_from' => $this->order_from,
            'payment_type' => $this->payment_type,
            'payment_visa_status' => $this->payment_visa_status ?? '',
            'payment_reference' => $this->payment_reference ?? '',
            'products' => OrderProductResource::collection($this->whenLoaded('products')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'promo' => [
                'code' => $this->customer_promo_code_title,
                'discount_value' => $this->customer_promo_code_value ? (float) $this->customer_promo_code_value : null,
                'discount_type' => $this->customer_promo_code_type,
            ],
            'pricing' => [
                'items_count' => $this->items_count,
                'total_product_price' => (float) $this->total_product_price,
                'total_tax' => (float) $this->total_tax,
                'shipping' => (float) $this->shipping,
                'extra_fees_discounts' => ExtraFeeDiscountResource::collection($this->whenLoaded('extraFeesDiscounts')),
                'total_price' => (float) $this->total_price,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
