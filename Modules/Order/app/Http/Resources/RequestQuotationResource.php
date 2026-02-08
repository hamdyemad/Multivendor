<?php

namespace Modules\Order\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Customer\app\Transformers\CustomerApiResource;
use Modules\Customer\app\Transformers\AddressResource;
use Modules\Order\app\Http\Resources\Api\OrderResource;

class RequestQuotationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'quotation_number' => $this->quotation_number,
            'notes' => $this->notes,
            'file' => $this->file ? asset('storage/' . $this->file) : null,
            'created_at' => $this->created_at,
            // Multi-vendor data
            'vendors' => $this->whenLoaded('vendors', fn() => RequestQuotationVendorResource::collection($this->vendors)),
        ];
    }
}
