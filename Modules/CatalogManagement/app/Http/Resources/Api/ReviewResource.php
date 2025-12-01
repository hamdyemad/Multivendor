<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'reviewable_id' => $this->reviewable_id,
            'reviewable_type' => $this->reviewable_type == "VendorProduct" ? "products" : "vendors",
            'customer_id' => $this->customer_id,
            'customer' => [
                'id' => $this->customer?->id,
                'name' => $this->customer?->full_name,
                'email' => $this->customer?->email,
            ],
            'review' => $this->review,
            'star' => $this->star,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
