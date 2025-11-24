<?php

namespace Modules\Customer\app\Actions;

use Modules\Customer\app\Models\Customer;

class UpdateCustomerPasswordAction
{
    public function execute(Customer $customer, string $newPassword): Customer
    {
        $customer->update(['password' => $newPassword]);

        return $customer;
    }
}
