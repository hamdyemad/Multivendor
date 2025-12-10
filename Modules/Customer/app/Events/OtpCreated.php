<?php

namespace Modules\Customer\app\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OtpCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ?object $customer,
        public string $otp,
        public string $type,
        public ?int $expiresInMinutes = 10,
        public ?string $verificationToken = null,
    ) {}
}
