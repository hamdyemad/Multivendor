<?php

namespace Modules\Order\app\Repositories\Api;

use Modules\Order\app\Interfaces\Api\RequestQuotationApiRepositoryInterface;
use Modules\Order\app\Models\RequestQuotation;

class RequestQuotationApiRepository implements RequestQuotationApiRepositoryInterface
{
    public function getCustomerQuotations(int $customerId, int $perPage = 15, array $filters = [])
    {
        $query = RequestQuotation::with(['customer', 'customerAddress.city', 'customerAddress.region', 'customerAddress.subregion', 'customerAddress.country', 'order'])
            ->where('customer_id', $customerId);

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Search by keyword (order number, customer data)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($q2) use ($search) {
                        $q2->where('order_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('customer', function ($q2) use ($search) {
                        $q2->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function findForCustomer(int $id, int $customerId)
    {
        return RequestQuotation::with(['customer', 'customerAddress.city', 'customerAddress.region', 'customerAddress.subregion', 'customerAddress.country', 'order'])
            ->where('id', $id)
            ->where('customer_id', $customerId)
            ->first();
    }

    public function create(array $data)
    {
        $quotation = RequestQuotation::create($data);
        $quotation->load('customer', 'customerAddress.city', 'customerAddress.region', 'customerAddress.subregion', 'customerAddress.country');
        return $quotation;
    }

    public function acceptOffer(int $id, int $customerId)
    {
        $quotation = RequestQuotation::findOrFail($id);
        
        $quotation->update([
            'status' => RequestQuotation::STATUS_ORDER_CREATED,
            'offer_responded_at' => now(),
            'customer_id' => $customerId,
        ]);

        return $quotation->fresh();
    }

    public function rejectOffer(int $id, int $customerId)
    {
        $quotation = RequestQuotation::findOrFail($id);
        
        $quotation->update([
            'status' => RequestQuotation::STATUS_REJECTED_OFFER,
            'offer_responded_at' => now(),
            'customer_id' => $customerId,
        ]);

        return $quotation->fresh();
    }
}
