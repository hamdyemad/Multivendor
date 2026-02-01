<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        // Only enable Telescope in local and staging environments
        // Disable completely in production for security
        if ($this->app->environment('production')) {
            Telescope::stopRecording();
        }

        $isLocal = $this->app->environment('local');

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            // Ignore heavy data import routes to prevent memory issues
            $uri = request()->getRequestUri();
            if (str_contains($uri, 'inject-data') || str_contains($uri, 'inject-products')) {
                return false;
            }
            
            return $isLocal ||
                   $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        // Hide sensitive request parameters
        Telescope::hideRequestParameters([
            '_token',
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
            'api_key',
            'api_secret',
            'secret',
            'token',
            'access_token',
            'refresh_token',
        ]);

        // Hide sensitive request headers
        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
            'authorization',
            'php-auth-pw',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            // Allow access in local environment
            if (app()->environment('local')) {
                return true;
            }

            // Check if user is authenticated
            if (!$user) {
                return false;
            }

            // Option 1: Check by email (for specific users)
            $allowedEmails = [
                'super_admin@gmail.com',
                'admin@eramo.com',
                // Add more admin emails here
            ];

            if (in_array($user->email, $allowedEmails)) {
                return true;
            }

            // Option 2: Check by user type (if you have user_type_id)
            // Assuming user_type_id = 1 is admin
            if (isset($user->user_type_id) && $user->user_type_id == 1) {
                return true;
            }

            // Option 3: Check by role name (if using Spatie or similar)
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                return true;
            }

            // Deny access by default
            return false;
        });
    }
}
