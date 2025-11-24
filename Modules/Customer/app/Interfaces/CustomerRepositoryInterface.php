<?php

namespace Modules\Customer\app\Interfaces;

use Modules\Customer\app\Models\Customer;
use Modules\Customer\app\Models\CustomerOtp;
use Modules\Customer\app\Models\CustomerPasswordResetToken;

interface CustomerRepositoryInterface
{
    public function getAllCustomers(array $filters = []);

    public function findById(array $filters = [], $id);
    public function getCustomersQuery(array $filters = []);

    public function getCustomerById(int $id): ?Customer;

    public function createCustomer(array $data);

    public function updateCustomer(int $id, array $data);

    public function deleteCustomer(int $id);

    public function createOtp(string $email, string $otp, string $type): CustomerOtp;

    public function getByEmail(string $email): ?Customer;

    public function create(array $data): Customer;

    public function createPasswordResetToken(string $email, string $token): CustomerPasswordResetToken;

    public function getPasswordResetToken(string $email, string $token): ?CustomerPasswordResetToken;

    public function deletePasswordResetToken(string $email): void;

    public function getCustomerCount(): int;
}

