<?php

namespace Modules\Customer\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\Customer\app\Http\Requests\Api\CreateAddressRequest;
use Modules\Customer\app\Http\Requests\Api\UpdateAddressRequest;
use Modules\Customer\app\Transformers\AddressResource;
use Modules\Customer\app\Services\Api\CustomerAddressService;

class CustomerAddressController extends Controller
{
    use Res;

    public function __construct(protected CustomerAddressService $addressService)
    {}

    /**
     * Create a new address
     */
    public function store(CreateAddressRequest $request)
    {
        $validated = $request->validated();
        $address = $this->addressService->createAddress($request->user(), $validated);

        return $this->sendRes(
            config('responses.address_created')[app()->getLocale()] ?? 'Address created successfully',
            true,
            AddressResource::make($address),
            [],
            201
        );
    }

    /**
     * Get all addresses for customer
     */
    public function index(Request $request)
    {
        $addresses = $this->addressService->getAddresses($request->all(), $request->user());

        return $this->sendRes(
            config('responses.addresses_retrieved')[app()->getLocale()] ?? 'Addresses retrieved successfully',
            true,
            AddressResource::collection($addresses)
        );
    }

    /**
     * Get single address
     */
    public function show(Request $request, $addressId)
    {
        $address = $this->addressService->getAddressById($addressId, $request->user());

        if (!$address) {
            return $this->sendRes(
                config('responses.not_found')[app()->getLocale()] ?? 'Address not found',
                false,
                [],
                [],
                404
            );
        }

        return $this->sendRes(
            config('responses.address_retrieved')[app()->getLocale()] ?? 'Address retrieved successfully',
            true,
            AddressResource::make($address)
        );
    }

    /**
     * Update an address
     */
    public function update(UpdateAddressRequest $request, $addressId)
    {
        $validated = $request->validated();
        $address = $this->addressService->updateAddress($addressId, $request->user(), $validated);

        return $this->sendRes(
            config('responses.address_updated')[app()->getLocale()] ?? 'Address updated successfully',
            true,
            AddressResource::make($address)
        );
    }

    /**
     * Delete an address
     */
    public function destroy(Request $request, $addressId)
    {
        $deleted = $this->addressService->deleteAddress($addressId, $request->user());

        if (!$deleted) {
            return $this->sendRes(
                config('responses.not_found')[app()->getLocale()] ?? 'Address not found',
                false,
                [],
                [],
                404
            );
        }

        return $this->sendRes(
            config('responses.address_deleted')[app()->getLocale()] ?? 'Address deleted successfully',
            true
        );
    }
}
