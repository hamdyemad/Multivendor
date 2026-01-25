<?php

namespace Modules\Refund\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RefundRequestHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // Determine who made the change
        $actorName = null;
        $actorType = null;
        
        if ($this->user_id && $this->user) {
            $actorName = $this->user->name;
            $actorType = 'user'; // Admin or Vendor
        } elseif ($this->customer_id && $this->customer) {
            $actorName = $this->customer->name;
            $actorType = 'customer';
        }
        
        // Translate notes if it's a translation key
        $notes = $this->notes;
        if ($notes && str_starts_with($notes, 'refund::')) {
            $notes = trans($notes);
        }
        
        return [
            'id' => $this->id,
            'refund_request_id' => $this->refund_request_id,
            'old_status' => $this->old_status,
            'old_status_label' => $this->old_status ? trans('refund::refund.statuses.' . $this->old_status) : null,
            'new_status' => $this->new_status,
            'new_status_label' => trans('refund::refund.statuses.' . $this->new_status),
            'user_id' => $this->user_id,
            'user_name' => $this->user?->name,
            'customer_id' => $this->customer_id,
            'customer_name' => $this->customer?->name,
            'actor_name' => $actorName,
            'actor_type' => $actorType,
            'notes' => $notes,
            'created_at' => $this->created_at,
        ];
    }
}
