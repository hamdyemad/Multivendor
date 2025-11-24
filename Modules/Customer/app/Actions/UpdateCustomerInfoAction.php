<?php

namespace Modules\Customer\app\Actions;

use Modules\Customer\app\Models\Customer;

class UpdateCustomerInfoAction
{
    public function execute(Customer $customer, array $data): Customer
    {
        $updateData = [];

        if (isset($data['first_name'])) {
            $updateData['first_name'] = $data['first_name'];
        }

        if (isset($data['last_name'])) {
            $updateData['last_name'] = $data['last_name'];
        }

        if (isset($data['phone'])) {
            $updateData['phone'] = $data['phone'];
        }

        if (isset($data['lang'])) {
            $updateData['lang'] = $data['lang'];
        }

        if (!empty($updateData)) {
            $customer->update($updateData);
        }

        return $customer;
    }
}
