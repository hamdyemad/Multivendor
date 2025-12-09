<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoCodeResource extends JsonResource
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
            'code' => $this->code,
            'discount_type' => $this->type,
            'discount_value' => (float) $this->value,
            'valid_until' => $this->valid_until_api,
            'valid_from' => $this->valid_from_api,
            'maximum_times_of_use' => $this->maximum_of_use,
        ];
    }
}
