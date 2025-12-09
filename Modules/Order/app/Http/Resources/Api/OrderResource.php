<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'customer_address' => $this->customer_address,
            'order_from' => $this->order_from,
            'payment_type' => $this->payment_type,
            'stage' => $this->stage?->name,
            'items_count' => $this->items_count,
            'total_product_price' => (float) $this->total_product_price,
            'total_tax' => (float) $this->total_tax,
            'total_fees' => (float) $this->total_fees,
            'total_discounts' => (float) $this->total_discounts,
            'shipping' => (float) $this->shipping,
            'total_price' => (float) $this->total_price,
            'promo_code' => $this->customer_promo_code_title,
            'promo_discount' => $this->customer_promo_code_amount ? (float) $this->customer_promo_code_amount : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
