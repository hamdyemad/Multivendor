<?php

namespace Modules\Customer\app\Services;

use Modules\Customer\app\Interfaces\CustomerRepositoryInterface;

class CustomerService
{
    protected $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Get all customers with filters and pagination
     */
    public function getAllCustomers(array $filters = [])
    {
        return $this->customerRepository->getAllCustomers($filters);
    }

    /**
     * Get customers query for DataTable
     */
    public function getCustomersQuery(array $filters = [])
    {
        return $this->customerRepository->getCustomersQuery($filters);
    }

    /**
     * Get customer by ID
     */
    public function findById(array $filters = [], $id)
    {
        return $this->customerRepository->findById($filters, $id);
    }

    /**
     * Create a new customer
     */
    public function createCustomer(array $data)
    {
        return $this->customerRepository->createCustomer($data);
    }

    /**
     * Update customer
     */
    public function updateCustomer(int $id, array $data)
    {
        return $this->customerRepository->updateCustomer($id, $data);
    }

    /**
     * Delete customer
     */
    public function deleteCustomer(int $id)
    {
        return $this->customerRepository->deleteCustomer($id);
    }

    /**
     * Get customer with full details by ID
     */
    public function getCustomerWithDetails($customerId)
    {
        return $this->customerRepository->getCustomerWithDetails($customerId);
    }

    /**
     * Get customer address by ID
     */
    public function getCustomerAddress($addressId)
    {
        return $this->customerRepository->getCustomerAddress($addressId);
    }

    /**
     * Get all customer addresses
     */
    public function getCustomerAddresses($customerId)
    {
        return $this->customerRepository->getCustomerAddresses($customerId);
    }
}
