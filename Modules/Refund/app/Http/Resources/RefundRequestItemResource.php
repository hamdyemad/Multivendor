<?php

namespace Modules\Refund\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RefundRequestItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'refund_request_id' => $this->refund_request_id,
            'order_product_id' => $this->order_product_id,
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'total_price' => (float) $this->total_price,
            'reason' => $this->reason,
            
            // Product Variant details (accessed through orderProduct)
            'variant' => $this->whenLoaded('orderProduct', function () {
                $variant = $this->orderProduct?->vendorProductVariant;
                if (!$variant) {
                    return null;
                }
                return [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'price' => (float) $variant->price,
                    'stock' => $variant->stock,
                    'variant_values' => $variant->variant_values ?? [],
                    'image' => $variant->image_url ?? null,
                ];
            }),
            
            // Order Product details (includes product info)
            'order_product' => $this->whenLoaded('orderProduct', function () {
                return [
                    'id' => $this->orderProduct->id,
                    'product_id' => $this->orderProduct->product_id,
                    'product_name' => $this->orderProduct->product_name,
                    'product_sku' => $this->orderProduct->product_sku,
                    'variant_details' => $this->orderProduct->variant_details ?? null,
                    'price' => (float) $this->orderProduct->price,
                    'quantity' => $this->orderProduct->quantity,
                ];
            }),
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
