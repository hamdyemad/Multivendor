<?php

namespace Modules\Vendor\app\Services\Api;

use Illuminate\Notifications\AnonymousNotifiable;
use Modules\Vendor\app\Interfaces\Api\VendorRequestRepositoryInterface;
use Modules\Vendor\app\Notifications\VendorRequestNotification;

class VendorRequestService
{
    protected $vendorRequestRepository;

    public function __construct(VendorRequestRepositoryInterface $vendorRequestRepository)
    {
        $this->vendorRequestRepository = $vendorRequestRepository;
    }

    /**
     * Create a new vendor request
     */
    public function createVendorRequest(array $data)
    {
        $vendorRequest = $this->vendorRequestRepository->createVendorRequest($data);

        // Send notification email to vendor
        $notifiable = new AnonymousNotifiable();
        $notifiable->route('mail', $vendorRequest->email);
        $notifiable->notify(new VendorRequestNotification($vendorRequest));

        return $vendorRequest;
    }

    /**
     * Get all vendor requests with filters and pagination
     */
    public function getAllVendorRequests(array $filters = [], int $perPage = 10)
    {
        return $this->vendorRequestRepository->getAllVendorRequests($filters, $perPage);
    }

    /**
     * Get vendor request by ID
     */
    public function getVendorRequestById(int $id)
    {
        return $this->vendorRequestRepository->getVendorRequestById($id);
    }

    /**
     * Update vendor request
     */
    public function updateVendorRequest(int $id, array $data)
    {
        return $this->vendorRequestRepository->updateVendorRequest($id, $data);
    }

    /**
     * Delete vendor request
     */
    public function deleteVendorRequest(int $id)
    {
        return $this->vendorRequestRepository->deleteVendorRequest($id);
    }

    /**
     * Approve vendor request
     */
    public function approveVendorRequest(int $id)
    {
        return $this->vendorRequestRepository->approveVendorRequest($id);
    }

    /**
     * Reject vendor request
     */
    public function rejectVendorRequest(int $id, string $reason = null)
    {
        return $this->vendorRequestRepository->rejectVendorRequest($id, $reason);
    }
}
