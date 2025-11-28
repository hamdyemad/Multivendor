<?php

namespace Modules\Vendor\app\Services\Api;

use Illuminate\Notifications\AnonymousNotifiable;
use Modules\Vendor\app\DTOs\VendorFilterDTO;
use Modules\Vendor\app\Interfaces\Api\VendorApiRepositoryInterface;
use Modules\Vendor\app\Notifications\VendorRequestNotification;

class VendorApiService
{
    protected $VendorRepository;

    public function __construct(VendorApiRepositoryInterface $VendorRepository)
    {
        $this->VendorRepository = $VendorRepository;
    }

    /**
     * Get all Vendors with filters and pagination
     */
    public function getAllVendors(VendorFilterDTO $dto)
    {
        return $this->VendorRepository->getAllVendors($dto);
    }

    /**
     * Get Vendor by ID
     */
    public function find(VendorFilterDTO $dto, $id)
    {
        return $this->VendorRepository->find($dto, $id);
    }

    /**
     * Create a new vendor request
     */
    public function createVendorRequest(array $data)
    {
        $vendorRequest = $this->VendorRepository->createVendorRequest($data);

        // Send notification email to vendor
        $notifiable = new AnonymousNotifiable();
        $notifiable->route('mail', $vendorRequest->email);
        $notifiable->notify(new VendorRequestNotification($vendorRequest));

        return $vendorRequest;
    }
}
