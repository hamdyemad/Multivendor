<?php

namespace Modules\Customer\app\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Customer\app\Transformers\AddressResource;

class CustomerApiResource extends JsonResource
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
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'lang' => $this->lang,
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
            'status' => (bool) $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
