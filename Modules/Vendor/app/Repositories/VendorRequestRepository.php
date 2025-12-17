<?php

namespace Modules\Vendor\app\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Vendor\app\Interfaces\VendorRequestRepositoryInterface;
use Modules\Vendor\app\Models\VendorRequest;

class VendorRequestRepository implements VendorRequestRepositoryInterface
{
    /**
     * Create a new vendor request
     */
    public function createVendorRequest(array $data)
    {
        return DB::transaction(function () use ($data) {
            $vendorRequest = VendorRequest::create([
                'email' => $data['email'],
                'phone' => $data['phone'],
                'company_name' => $data['company_name'],
                'status' => 'pending',
            ]);


            return $vendorRequest;
        });
    }

    /**
     * Get all vendor requests with filters and pagination
     */
    public function getAllVendorRequests(array $filters = [], int $perPage = 10)
    {
        $query = VendorRequest::query();

        // Search by email or company name
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        // Filter by email
        if (!empty($filters['email'])) {
            $query->byEmail($filters['email']);
        }


        // Filter by created date from
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        // Filter by created date to
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        // Sort by latest
        $query->latest();

        return ($perPage == 0) ? $query->get() : $query->paginate($perPage);
    }

    /**
     * Get vendor request by ID
     */
    public function getVendorRequestById(int $id)
    {
        return VendorRequest::findOrFail($id);
    }

    /**
     * Update vendor request
     */
    public function updateVendorRequest(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $vendorRequest = VendorRequest::findOrFail($id);

            $vendorRequest->update([
                'email' => $data['email'] ?? $vendorRequest->email,
                'phone' => $data['phone'] ?? $vendorRequest->phone,
                'company_name' => $data['company_name'] ?? $vendorRequest->company_name,
            ]);


            return $vendorRequest;
        });
    }

    /**
     * Delete vendor request (soft delete)
     */
    public function deleteVendorRequest(int $id)
    {
        return DB::transaction(function () use ($id) {
            $vendorRequest = VendorRequest::findOrFail($id);
            return $vendorRequest->delete();
        });
    }

    /**
     * Approve vendor request
     */
    public function approveVendorRequest(int $id)
    {
        return DB::transaction(function () use ($id) {
            $vendorRequest = VendorRequest::findOrFail($id);
            $vendorRequest->update(['status' => 'approved']);
            return $vendorRequest;
        });
    }

    /**
     * Reject vendor request
     */
    public function rejectVendorRequest(int $id, ?string $reason = null)
    {
        return DB::transaction(function () use ($id, $reason) {
            $vendorRequest = VendorRequest::findOrFail($id);
            $vendorRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
            ]);
            return $vendorRequest;
        });
    }
}
