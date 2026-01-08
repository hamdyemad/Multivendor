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
            'notes' => $this->notes,
            'file' => $this->file ? asset('storage/' . $this->file) : null,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'offer_sent_at' => $this->offer_sent_at,
            'offer_responded_at' => $this->offer_responded_at,
            'can_respond' => $this->canRespondToOffer(),
            'address' => $this->whenLoaded('customerAddress', fn() => new AddressResource($this->customerAddress)),
            'customer' => $this->whenLoaded('customer', fn() => new CustomerApiResource($this->customer)),
            'order' => $this->whenLoaded('order', fn() => new OrderResource($this->order)),
            'created_at' => $this->created_at,
        ];
    }

    protected function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => __('order::request-quotation.status_pending'),
            'sent_offer' => __('order::request-quotation.status_sent_offer'),
            'accepted_offer' => __('order::request-quotation.status_accepted_offer'),
            'rejected_offer' => __('order::request-quotation.status_rejected_offer'),
            'order_created' => __('order::request-quotation.status_order_created'),
            'archived' => __('order::request-quotation.status_archived'),
            default => $this->status ?? '',
        };
    }
}
