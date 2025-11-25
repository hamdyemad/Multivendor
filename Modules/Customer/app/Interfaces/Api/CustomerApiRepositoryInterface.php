<?php

namespace Modules\Customer\app\Interfaces\Api;

use Modules\Customer\app\Models\Customer;
use Modules\Customer\app\Models\CustomerOtp;
use Modules\Customer\app\Models\CustomerPasswordResetToken;

interface CustomerApiRepositoryInterface
{
    public function getByEmail(string $email): ?Customer;

    public function create(array $data): Customer;

    public function createOtp(string $email, string $otp, string $type, int $expiresInMinutes = 10, ?string $verificationToken = null): CustomerOtp;

    public function verifyEmail(Customer $customer): void;

    public function getPasswordResetToken(string $email, string $token): ?CustomerPasswordResetToken;

    public function createPasswordResetToken(string $email, string $token): CustomerPasswordResetToken;

    public function deletePasswordResetToken(string $email): void;

    public function createTokens(Customer $customer, ?string $fcmToken = null, ?string $deviceId = null): array;

    public function getByToken(string $token): ?Customer;

    public function revokeTokens(Customer $customer, ?string $deviceId = null): void;

    public function getProfile(Customer $customer): Customer;

    public function updateInfo(Customer $customer, array $data): Customer;

    public function updatePassword(Customer $customer, string $newPassword): Customer;

    public function changeLanguage(Customer $customer, string $lang): Customer;

}

