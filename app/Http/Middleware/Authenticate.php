<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     */
    public function handle($request, \Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        // Skip additional checks for API requests with sanctum guard
        $isApiRequest = $request->expectsJson() || $request->is('api/*');
        
        // Check if authenticated user is inactive
        $user = Auth::user();
        if ($user && !$user->active) {
            if (!$isApiRequest) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', __('auth.account_not_activated'));
            }
            
            throw new AuthenticationException(
                __('auth.account_not_activated'),
                $guards
            );
        }

        // Check if authenticated user is blocked
        if ($user && $user->block) {
            if (!$isApiRequest) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', __('auth.account_blocked'));
            }
            
            throw new AuthenticationException(
                __('auth.account_blocked'),
                $guards
            );
        }

        // Check if user is a vendor (has vendor relationship via user_id, not vendor_id)
        // and if the vendor is inactive, logout the user
        // Only check for web users, not API customers
        if (!$isApiRequest && $user && !$user->vendor_id) {
            $vendor = $user->vendorByUser;
            if ($vendor && !$vendor->active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', __('auth.vendor_not_activated'));
            }
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * Handle unauthenticated requests for API routes
     */
    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson()) {
            throw new AuthenticationException(
                config('responses.unauthorized')[app()->getLocale()],
                $guards
            );
        }

        parent::unauthenticated($request, $guards);
    }
}
