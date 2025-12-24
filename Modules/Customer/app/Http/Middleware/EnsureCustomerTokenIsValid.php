<?php

namespace Modules\Customer\app\Http\Middleware;

use App\Traits\Res;
use Closure;
use Illuminate\Http\Request;
use Modules\Customer\app\Models\Customer;
use Modules\Customer\app\Models\CustomerAccessToken;

class EnsureCustomerTokenIsValid
{
    use Res;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the authorization header
        $token = $request->bearerToken();

        if (!$token) {
            return $this->sendRes(
                config('responses.unauthorized')[app()->getLocale()],
                false,
                [],
                ['error' => config('responses.unauthorized')[app()->getLocale()]],
                401
            );
        }

        // Find the token in customer_access_tokens
        $tokenHash = hash('sha256', $token);
        $accessToken = CustomerAccessToken::where('token', $tokenHash)->first();

        if (!$accessToken) {
            return $this->sendRes(
                config('responses.invalid_token')[app()->getLocale()],
                false,
                [],
                ['error' => config('responses.invalid_token')[app()->getLocale()]],
                401
            );
        }

        // Check if token has expired
        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return $this->sendRes(
                config('responses.expired_token')[app()->getLocale()],
                false,
                [],
                ['error' => config('responses.expired_token')[app()->getLocale()]],
                401
            );
        }

        // Get the customer
        $customer = $accessToken->customer;

        if (!$customer) {
            return $this->sendRes(
                config('responses.user_not_found')[app()->getLocale()],
                false,
                [],
                ['error' => config('responses.user_not_found')[app()->getLocale()]],
                401
            );
        }

        // Set the authenticated user using the sanctum guard
        auth()->guard('sanctum')->setUser($customer);
        $request->setUserResolver(function () use ($customer) {
            return $customer;
        });

        // Update last_used_at
        $accessToken->update(['last_used_at' => now()]);

        return $next($request);
    }
}
