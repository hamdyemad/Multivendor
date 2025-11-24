<?php

namespace Modules\Customer\app\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Customer\app\Events\OtpCreated;
use Modules\Customer\app\Events\CustomerEmailVerified;
use Modules\Customer\app\Listeners\SendOtpEmail;
use Modules\Customer\app\Listeners\SendWelcomeNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        OtpCreated::class => [
            SendOtpEmail::class,
        ],
        CustomerEmailVerified::class => [
            SendWelcomeNotification::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
