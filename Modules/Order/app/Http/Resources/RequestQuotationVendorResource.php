<?php

namespace Modules\Order\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Order\app\Http\Resources\Api\OrderResource;

class RequestQuotationVendorResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'request_quotation_id' => $this->request_quotation_id,
            'vendor_id' => $this->vendor_id,
            'vendor' => [
                'id' => $this->vendor?->id,
                'name' => $this->vendor?->name,
                'logo' => $this->vendor?->logo ? asset('storage/' . $this->vendor->logo->path) : null,
            ],
            'status' => $this->status,
            'status_label' => $this->status_label,
            'offer_price' => $this->offer_price,
            'offer_notes' => $this->offer_notes,
            'offer_sent_at' => $this->offer_sent_at?->toISOString(),
            'can_respond' => $this->canRespondToOffer(), // Customer can accept/reject if status is offer_sent
            'order' => $this->whenLoaded('order', function() {
                return $this->order ? new OrderResource($this->order) : null;
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
