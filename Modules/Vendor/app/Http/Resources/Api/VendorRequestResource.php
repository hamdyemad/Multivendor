<?php

namespace Modules\Vendor\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CategoryManagment\app\Http\Resources\Api\ActivityApiResource;

class VendorRequestResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'company_name' => $this->company_name,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'activities' => ActivityApiResource::collection($this->activities),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
