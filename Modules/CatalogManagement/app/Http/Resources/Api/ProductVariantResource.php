<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'price' => $this->price,
            'discount_percentage' => $this->discount_percentage,
            'discount_end_date' => $this->discount_end_date,
            'stock' => $this->stock,
            'is_active' => $this->is_active,
            'configuration' => $this->whenLoaded('configuration'),
        ];
    }
}
