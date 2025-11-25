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
use Modules\Customer\app\Models\CustomerOtp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerAuthService
{
    public function __construct(
        protected CustomerApiRepositoryInterface $customerRepository,
        protected ValidateOtpAction $validateOtpAction,
    ) {}

    public function saveOtp(string $email, string $otp, string $type, int $expiresInMinutes = 10, ?string $verificationToken = null)
    {
        return $this->customerRepository->createOtp($email, $otp, $type, $expiresInMinutes, $verificationToken);
    }

    /**
     * Send OTP for email verification
     */
    public function sendEmailVerificationOtp(string $email, $cause = "email_verification", int $expiresInMinutes = 10)
    {
        // Check if customer exists
        $customer = $this->customerRepository->getByEmail($email);

        if (!$customer || $customer->hasVerifiedEmail()) {
            return false;
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $verificationToken = CustomerOtp::generateVerificationToken();

        event(new OtpCreated($customer, $otp, $cause, $expiresInMinutes, $verificationToken));

        return [
            'otp' => $otp,
            'verification_token' => $verificationToken
        ];
    }

    /**
     * Register customer (save to DB first, then send OTP)
     */
    public function registerCustomer(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $customer = $this->customerRepository->create($data);

            // Send OTP after creating customer
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $verificationToken = CustomerOtp::generateVerificationToken();

            event(new OtpCreated($customer, $otp, "email_verification", 10, $verificationToken));

            return [
                'otp' => $otp,
                'verification_token' => $verificationToken
            ];
        });
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
     * Verify email via token (from email button link)
     */
    public function verifyEmailToken(string $token): bool
    {
        Log::info('=== EMAIL VERIFICATION STARTED ===', ['token' => substr($token, 0, 10) . '...']);

        return DB::transaction(function () use ($token) {
            // Find the OTP record by verification token
            Log::info('Searching for OTP record', ['token' => substr($token, 0, 10) . '...']);

            $otp = CustomerOtp::where('verification_token', $token)
                ->where('type', 'email_verification')
                ->where('expires_at', '>', now())
                ->whereNull('verified_at')
                ->first();

            if (!$otp) {
                Log::warning('OTP record not found', [
                    'token' => substr($token, 0, 10) . '...',
                    'reason' => 'Token not found, expired, or already verified'
                ]);
                return false;
            }

            Log::info('OTP record found', [
                'otp_id' => $otp->id,
                'email' => $otp->email,
                'type' => $otp->type,
                'expires_at' => $otp->expires_at
            ]);

            // Mark OTP as verified
            Log::info('Marking OTP as verified', ['otp_id' => $otp->id]);
            $otp->markAsVerified();

            // Get customer by email
            Log::info('Fetching customer', ['email' => $otp->email]);
            $customer = $this->customerRepository->getByEmail($otp->email);

            if (!$customer) {
                Log::warning('Customer not found', ['email' => $otp->email]);
                return false;
            }

            if (!$customer->status) {
                Log::warning('Customer is inactive', ['customer_id' => $customer->id, 'email' => $customer->email]);
                return false;
            }

            Log::info('Customer found and active', [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'email_verified_at' => $customer->email_verified_at
            ]);

            // Verify email
            Log::info('Verifying customer email', ['customer_id' => $customer->id]);
            $this->customerRepository->verifyEmail($customer);

            // Refresh customer to get updated data
            $customer->refresh();
            Log::info('Email verified successfully', [
                'customer_id' => $customer->id,
                'email_verified_at' => $customer->email_verified_at
            ]);

            // Dispatch event to send welcome notification
            Log::info('Dispatching CustomerEmailVerified event', ['customer_id' => $customer->id]);
            event(new CustomerEmailVerified($customer));

            Log::info('=== EMAIL VERIFICATION COMPLETED SUCCESSFULLY ===', ['customer_id' => $customer->id]);
            return true;
        });
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


