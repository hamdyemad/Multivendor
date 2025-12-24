<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Auth\AuthenticationException;

class Authenticate extends Middleware
{
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
