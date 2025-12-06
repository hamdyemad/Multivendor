<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Modules\Customer\app\Models\Customer;
use Modules\Customer\app\Models\CustomerAddress;
use Modules\Customer\app\Services\Api\CustomerAddressService;
use Modules\Customer\app\Services\Api\CustomerAuthService;

class FetchUserData
{

    public function __construct(
        private CustomerAuthService $customerService,
        private CustomerAddressService $customerAddressService,
    ) {}

    /**
     * Handle the pipeline.
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        // Determine if customer is existing or external
        if ($data['customer_type'] === 'existing') {
            $customer = $this->customerService->getById($data['selected_customer_id']);

            $address = $this->customerAddressService->getAddressById($data['customer_address_id'], $customer);

            $context['customer'] = [
                'id' => $customer->id,
                'name' => $customer->full_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address_id' => $address->id,
                'address' => $address->address,
                'country_id' => $address->country_id,
                'city_id' => $address->city_id,
                'region_id' => $address->region_id,
                'is_existing' => true,
            ];
        } else {
            // External customer
            $context['customer'] = [
                'id' => null,
                'name' => $data['external_customer_name'],
                'email' => $data['external_customer_email'],
                'phone' => $data['external_customer_phone'],
                'address' => $data['external_customer_address'],
                'address_id' => null,
                'country_id' => null,
                'city_id' => null,
                'region_id' => null,
                'is_existing' => false,
            ];
        }

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
