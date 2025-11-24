<?php

namespace Modules\Customer\app\Services\Api;

use Illuminate\Support\Facades\Hash;
use Modules\Customer\app\Actions\ValidateOtpAction;
use Modules\Customer\app\Models\Customer;
use Illuminate\Support\Str;
use Modules\Customer\app\Interfaces\Api\CustomerApiRepositoryInterface;
use Modules\Customer\app\Events\OtpCreated;
use Modules\Customer\app\Events\CustomerEmailVerified;
use App\Exceptions\InvalidPasswordException;

class CustomerAuthService
{
    public function __construct(
        protected CustomerApiRepositoryInterface $customerRepository,
        protected ValidateOtpAction $validateOtpAction,
    ) {}

    public function saveOtp(string $email, string $otp, string $type, int $expiresInMinutes = 10)
    {
        return $this->customerRepository->createOtp($email, $otp, $type, $expiresInMinutes);
    }

    /**
     * Send OTP for email verification
     */
    public function sendEmailVerificationOtp(string $email, $cause = "email_verification", int $expiresInMinutes = 10)
    {
        // Check if customer exists
        $customer = $this->customerRepository->getByEmail($email);

        // if (!$customer || $customer->hasVerifiedEmail()) {
        //     return false;
        // }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        event(new OtpCreated($customer, $otp, $cause, $expiresInMinutes));

        return [
            'otp' => $otp
        ];
    }

    /**
     * Register customer (save to DB first, then send OTP)
     */
    public function registerCustomer(array $data): array
    {
        $customer = $this->customerRepository->create($data);

        // Send OTP after creating customer
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        event(new OtpCreated($customer, $otp, "email_verification", 10));

        return [
            'otp' => $otp
        ];
    }

    /**
     * Verify email OTP (mark email as verified)
     */
    public function verifyEmailOtp(string $email, string $otp, $cause): bool
    {
        return $this->validateOtpAction->execute($email, $otp, $cause);
    }


    public function verifyOtp(array $data)
    {
        if(!$this->validateOtpAction->execute($data['email'], $data['otp'], 'email_verification')) {
            return false;
        }

        $customer = $this->customerRepository->getByEmail($data['email']);

        // Check if customer is inactive
        if (!$customer->status) {
            return false;
        }

        $this->customerRepository->verifyEmail($customer);

        // Dispatch event to send welcome notification
        event(new CustomerEmailVerified($customer));

        $tokens = $this->customerRepository->createTokens($customer, $data["fcm_token"] ?? null, $data["deviceId"] ?? null);
        return [
            "customer" => $customer,
            "tokens" => $tokens
        ];
    }

    /**
     * Send password reset OTP
     */
    public function sendPasswordResetOtp(string $email, int $expiresInMinutes = 10): bool
    {
        // Check if customer exists
        $customer = $this->customerRepository->getByEmail($email);
        if (!$customer) {
            return false;
        }

        event(new OtpCreated($customer, str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT), "password_reset", $expiresInMinutes));

        return true;
    }

    /**
     * Verify password reset OTP and generate reset token
     */
    public function verifyPasswordResetOtp(string $email, string $otp): ?string
    {
        if (!$this->validateOtpAction->execute($email, $otp, 'password_reset')) {
            return null;
        }

        $resetToken = Str::random(60);
        $this->customerRepository->createPasswordResetToken($email, $resetToken);

        return $resetToken;
    }

    /**
     * Generate access and refresh tokens
     */
    public function generateTokens(Customer $customer, ?string $fcmToken = null, ?string $deviceId = null): array
    {
        return $this->customerRepository->createTokens($customer, $fcmToken, $deviceId);
    }

    /**
     * Refresh access token using refresh token for a specific device
     */
    public function refreshAccessToken(array $tokens)
    {
        $customer = $this->customerRepository->getByToken($tokens['token']);
        if (!$customer) {
            return [];
        }
        $this->customerRepository->revokeTokens($customer, $tokens['device_id'] ?? null);

        return $this->customerRepository->createTokens($customer, $tokens['fcm_token'] ?? null, $tokens['device_id'] ?? null);
    }

    /**
     * Get password reset token (for validation)
     */
    public function resetPassword(array $data)
    {
        $resetToken = $this->customerRepository->getPasswordResetToken($data["email"], $data["reset_token"]);

        if (!$resetToken) {
            return null;
        }

        $this->customerRepository->deletePasswordResetToken($data["email"]);

        $customer = $this->customerRepository->getByEmail($data["email"]);

        if (!$customer) {
            return null;
        }

        $customer = $this->customerRepository->updatePassword($customer, $data["new_password"]);

        return [
            "customer" => $customer,
            "tokens" => $this->generateTokens($customer, $data["fcm_token"] ?? null, $data["device_id"] ?? null)
        ];
    }

    /**
     * Delete password reset token
     */
    public function deletePasswordResetToken(string $email): void
    {
        $this->customerRepository->deletePasswordResetToken($email);
    }

    /**
     * Logout customer from all devices
     */
    public function logoutDevices(Customer $customer): bool
    {
        $this->customerRepository->revokeTokens($customer);
        return true;
    }

    /**
     * Logout customer from a specific device
     */
    public function logout(Customer $customer, string $deviceId): bool
    {
        $this->customerRepository->revokeTokens($customer, $deviceId);
        return true;
    }

    public function login(array $data): array
    {
        $customer = $this->customerRepository->getByEmail($data['email']);

        if (!$customer || !Hash::check($data['password'], $customer->password)) {
            return [];
        }

        if (!$customer->status) {
            return [];
        }

        return [
            "customer" => $customer,
            "tokens" => $this->generateTokens($customer, $data['fcm_token'] ?? null, $data['device_id'] ?? null)
        ];
    }
}


