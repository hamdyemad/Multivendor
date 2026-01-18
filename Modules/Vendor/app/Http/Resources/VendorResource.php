<?php

namespace Modules\Vendor\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'logo' => $this->logo_url ?? null,
            'description' => $this->description ?? null,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
