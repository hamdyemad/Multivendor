<?php

namespace Modules\Order\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'paymob_payment_id' => $this->paymob_payment_id,
            'paymob_order_id' => $this->paymob_order_id,
            'payment_method' => $this->payment_method,
            'amount' => $this->amount,
            'amount_cents' => $this->amount_cents,
            'status' => $this->status,
            'transaction_id' => $this->transaction_id,
            'is_paid' => $this->isPaid(),
            'is_pending' => $this->isPending(),
            'is_failed' => $this->isFailed(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
