<?php

namespace Modules\Customer\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\Customer\app\Http\Requests\Api\CheckEmailRequest;
use Modules\Customer\app\Http\Requests\Api\RegisterRequest;
use Modules\Customer\app\Http\Requests\Api\VerifyOtpRequest;
use Modules\Customer\app\Http\Requests\Api\LoginRequest;
use Modules\Customer\app\Http\Requests\Api\VerifyResetOtpRequest;
use Modules\Customer\app\Http\Requests\Api\ResetPasswordRequest;
use Modules\Customer\app\Http\Requests\Api\TokensRequest;
use Modules\Customer\app\Http\Requests\Api\UpdateProfileRequest;
use Modules\Customer\app\Transformers\CustomerApiResource;
use Modules\Customer\app\Models\Customer;
use Modules\Customer\app\Services\Api\CustomerAuthService;
use Modules\Customer\app\Http\Requests\Api\DeviceIdRequest;

class CustomerAuthController extends Controller
{
    use Res;

    public function __construct(protected CustomerAuthService $authService)
    {}

    /**
     * STEP 1: Register customer (save to DB, check for duplication, send OTP)
     */
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        // Save customer to DB and send OTP
        $result = $this->authService->registerCustomer($validated);

        return $this->sendRes(
            config('responses.success_otp')[app()->getLocale()],
            true,
            $result,
            [],
            201
        );
    }

    /**
     * STEP 2: Verify OTP (only email + otp)
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $validated = $request->validated();

        $result = $this->authService->verifyOtp($validated);

        // Verify OTP only
        if (!$result) {
            // Check if customer exists and is inactive
            $customer = Customer::where('email', $validated['email'])->first();
            if ($customer && !$customer->status) {
                return $this->sendRes(
                    config('responses.customer_inactive')[app()->getLocale()],
                    false,
                    [],
                    [],
                    403
                );
            }

            return $this->sendRes(
                config('responses.invalied_otp')[app()->getLocale()],
                false,
                [],
                [],
                422
            );
        }

        return $this->sendRes(
            config('responses.verification_success')[app()->getLocale()],
            true,
            array_merge(
                CustomerApiResource::make($result["customer"])->resolve(),
                $result["tokens"]
            ),
            [],
            200
        );
    }

    /**
     * Verify email via token link (from email button)
     */
    public function verifyEmailToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $result = $this->authService->verifyEmailToken($request->token);

        if (!$result) {
            return $this->sendRes(
                config('responses.invalied_otp')[app()->getLocale()],
                false,
                [],
                [],
                422
            );
        }

        return $this->sendRes(
            config('responses.verification_success')[app()->getLocale()],
            true,
            [],
            [],
            200
        );
    }

    public function resendOtp(CheckEmailRequest $request)
    {
        $result = $this->authService->sendEmailVerificationOtp($request->validated()['email']);

        if (!$result) {
            return $this->sendRes(
                config('responses.invalied_or_verified')[app()->getLocale()],
                false,
                [],
                [],
                422
            );
        }

        return $this->sendRes(
            config('responses.opt_sent')[app()->getLocale()],
            true,
            $result,
            [],
            200
        );
    }

    /**
     * Login customer
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $result = $this->authService->login($validated);
        if(!$result) {
            return $this->sendRes(
                config('responses.invalid_credentials')[app()->getLocale()],
                false,
                [],
                [],
                401
            );
        }

        return $this->sendRes(
            config('responses.logged_in_success')[app()->getLocale()],
            true,
            array_merge(
                CustomerApiResource::make($result["customer"])->resolve(),
                $result["tokens"]
            )
        );
    }

    /**
     * STEP 1: Request password reset (send OTP)
     */
    public function requestPasswordReset(CheckEmailRequest $request)
    {
        $validated = $request->validated();

        // Send OTP
        $result = $this->authService->sendPasswordResetOtp($validated['email']);

        if (!$result) {
            return $this->sendRes(
                config('responses.invalied_email')[app()->getLocale()],
                false,
                [],
                [],
                422
            );
        }

        return $this->sendRes(
            config('responses.opt_sent')[app()->getLocale()],
            true,
            ['otp' => $result],
            [],
            200
        );
    }

    /**
     * STEP 2: Verify OTP for password reset (get reset token)
     */
    public function verifyPasswordResetOtp(VerifyResetOtpRequest $request)
    {
        $validated = $request->validated();

        // Verify OTP and get reset token
        $resetToken = $this->authService->verifyPasswordResetOtp($validated['email'], $validated['otp']);

        if (!$resetToken) {
            return $this->sendRes(
                config('responses.invalied_otp')[app()->getLocale()],
                false,
                [],
                [],
                422
            );
        }

        return $this->sendRes(
            config('responses.activated_otp')[app()->getLocale()],
            true,
            ['reset_token' => $resetToken],
            [],
            200
        );
    }

    /**
     * STEP 3: Reset password with reset token
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $validated = $request->validated();

        // Verify reset token is valid
        $result = $this->authService->resetPassword($validated);

        if (!$result) {
            return $this->sendRes(
                config('responses.invalied_reset_token')[app()->getLocale()],
                false,
                [],
                [],
                422
            );
        }

        return $this->sendRes(
            config('responses.password_reset_success')[app()->getLocale()],
            true,
            array_merge(
                CustomerApiResource::make($result["customer"])->resolve(),
                $result["tokens"]
            )
        );
    }

    /**
     * Refresh access token
     */
    public function refresh(TokensRequest $request)
    {
        $validated = $request->validated();
        $tokens = $this->authService->refreshAccessToken($validated);

        if(!$tokens) {
            return $this->sendRes(
                config('responses.invalid_token')[app()->getLocale()],
                false,
                [],
                [],
                422
            );
        }

        return $this->sendRes(
            config('responses.refreshed')[app()->getLocale()],
            true,
            $tokens
        );
    }

    /**
     * Logout customer from all devices
     */
    public function logoutDevices(Request $request)
    {
        $this->authService->logoutDevices($request->user());

        return $this->sendRes(
            config('responses.logout')[app()->getLocale()],
            true
        );
    }

    /**
     * Logout customer from a specific device
     */
    public function logout(DeviceIdRequest $request)
    {
        $deviceId = $request->validated()['device_id'];

        if (!$deviceId) {
            return $this->sendRes(
                config('responses.invalid_token')[app()->getLocale()],
                false,
                [],
                [
                    "device_id" => config('responses.invalid_deviceId')[app()->getLocale()],
                ],
                422
            );
        }

        $this->authService->logout($request->user(), $deviceId);

        return $this->sendRes(
            config('responses.logout')[app()->getLocale()],
            true
        );
    }
}

