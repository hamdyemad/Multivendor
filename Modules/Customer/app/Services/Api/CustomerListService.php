<?php

namespace Modules\Customer\app\Services\Api;

use Modules\Customer\app\Repositories\Api\CustomerApiRepository;

class CustomerListService
{
    public function __construct(
        protected CustomerApiRepository $repository
    ) {}

    /**
     * Get all customers with filters
     */
    public function getAllCustomers(array $filters = [], int $perPage = 15)
    {
        return $this->repository->getAllCustomers($filters, $perPage);
    }

    /**
     * Get customer with addresses
     */
    public function getCustomerWithAddresses(int $customerId)
    {
        return $this->repository->getCustomerWithAddresses($customerId);
    }

    /**
     * Get customer addresses
     */
    public function getCustomerAddresses(int $customerId)
    {
        return $this->repository->getCustomerAddresses($customerId);
    }
}
