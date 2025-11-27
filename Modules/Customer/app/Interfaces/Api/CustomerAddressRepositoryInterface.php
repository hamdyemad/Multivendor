<?php

namespace Modules\Customer\app\Interfaces\Api;

use Modules\Customer\app\DTOs\GetAddressesDTO;
use Modules\Customer\app\Models\Customer;
use Modules\Customer\app\Models\CustomerAddress;

interface CustomerAddressRepositoryInterface
{
    public function createAddress(Customer $customer, array $data): CustomerAddress;

    public function updateAddress($addressId, Customer $customer, array $data): CustomerAddress;

    public function getAddresses(GetAddressesDTO $dto, Customer $customer);

    public function getAddressById($addressId, Customer $customer): ?CustomerAddress;

    public function deleteAddress($addressId, Customer $customer): bool;
}
