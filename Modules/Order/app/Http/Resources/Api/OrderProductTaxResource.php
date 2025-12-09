<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductTaxResource extends JsonResource
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
            'tax_id' => $this->tax_id,
            'tax_name' => $this->tax?->name,
            'percentage' => (float) $this->percentage,
            'amount' => (float) $this->amount,
        ];
    }
}
