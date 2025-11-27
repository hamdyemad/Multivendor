<?php

namespace Modules\Customer\app\Services\Api;

use Modules\Customer\app\DTOs\GetAddressesDTO;
use Modules\Customer\app\Interfaces\Api\CustomerAddressRepositoryInterface;
use Modules\Customer\app\Models\Customer;

class CustomerAddressService
{
    public function __construct(protected CustomerAddressRepositoryInterface $addressRepository)
    {}

    /**
     * Create a new address for customer
     */
    public function createAddress(Customer $customer, array $data)
    {
        return $this->addressRepository->createAddress($customer, $data);
    }

    /**
     * Update customer address
     */
    public function updateAddress($addressId, Customer $customer, array $data)
    {
        return $this->addressRepository->updateAddress($addressId, $customer, $data);
    }

    /**
     * Get all addresses for customer
     */
    public function getAddresses(array $data, Customer $customer)
    {
        $dto = GetAddressesDTO::fromArray($data);
        return $this->addressRepository->getAddresses($dto, $customer);
    }

    /**
     * Get single address by ID
     */
    public function getAddressById($addressId, Customer $customer)
    {
        return $this->addressRepository->getAddressById($addressId, $customer);
    }

    /**
     * Delete address
     */
    public function deleteAddress($addressId, Customer $customer): bool
    {
        return $this->addressRepository->deleteAddress($addressId, $customer);
    }
}
