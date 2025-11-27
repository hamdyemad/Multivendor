<?php

namespace Modules\Customer\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\Customer\app\DTOs\GetAddressesDTO;
use Modules\Customer\app\Interfaces\Api\CustomerAddressRepositoryInterface;
use Modules\Customer\app\Models\Customer;
use Modules\Customer\app\Models\CustomerAddress;

class CustomerAddressRepository implements CustomerAddressRepositoryInterface
{

    public function __construct(private IsPaginatedAction $paginated){}


    public function createAddress(Customer $customer, array $data): CustomerAddress
    {
        if($data["is_primary"]){
            $customer->addresses()->update(["is_primary" => false]);
        }
        return $customer->addresses()->create($data);
    }

    public function updateAddress($addressId, Customer $customer, array $data): CustomerAddress
    {
        $address = $this->getAddressById($addressId, $customer);

        if($data["is_primary"]){
            $customer->addresses()->update(["is_primary" => false]);
        }

        $address->update($data);
        return $address;
    }

    public function getAddresses(GetAddressesDTO $dto, Customer $customer)
    {
        // Get addresses with filters applied
        $query = $customer->addresses()->filter($dto->getFilters());

        // Handle pagination
        $result = $this->paginated->handle($query, $dto->paginated, $dto->per_page);
        return $result;
    }

    public function getAddressById($addressId, Customer $customer): ?CustomerAddress
    {
        return $customer->addresses()->find($addressId);
    }

    public function deleteAddress($addressId, Customer $customer): bool
    {
        $address = $this->getAddressById($addressId, $customer);

        return $address->delete();
    }
}
