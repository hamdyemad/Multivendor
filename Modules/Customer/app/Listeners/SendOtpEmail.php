<?php

namespace Modules\Customer\app\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Customer\app\Events\OtpCreated;
use Modules\Customer\app\Mail\OtpMail;
use Modules\Customer\app\Models\Customer;
use Illuminate\Support\Facades\Mail;
use Modules\Customer\app\Models\CustomerOtp;
use Modules\Customer\app\Services\Api\CustomerAuthService;

class SendOtpEmail
{
    public function handle(OtpCreated $event): void
    {
        $customer = $event->custoemr;
        app(CustomerAuthService::class)->saveOtp(
            email: $customer->email,
            otp: $event->otp,
            type: $event->type,
            expiresInMinutes: $event->expiresInMinutes,
            verificationToken: $event->verificationToken ?? null,
        );

        $language = $customer?->language ?? 'en';

        if (!Mail::to($customer->email)->send(new OtpMail(
            email: $customer->email,
            otp: $event->otp,
            type: $event->type,
            expiresInMinutes: $event->expiresInMinutes,
            language: $language,
            verificationToken: $event->verificationToken ?? null,
        ))) {
            Log::error('Failed to send OTP email to ' . $customer->email);
            throw new \Exception('Could not send verification email');
        }
    }
}
