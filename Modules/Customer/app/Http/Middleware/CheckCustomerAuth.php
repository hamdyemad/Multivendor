<?php

namespace Modules\Customer\app\Http\Middleware;

use App\Traits\Res;
use Closure;
use Illuminate\Http\Request;

class CheckCustomerAuth
{
    use Res;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $customer = auth()->user();

        if (!$customer) {
            return $this->sendRes(
                config('responses.unauthorized')[app()->getLocale()],
                false,
                [],
                [],
                401
            );
        }

        if (!$customer->status) {
            return $this->sendRes(
                config('responses.customer_inactive')[app()->getLocale()],
                false,
                [],
                [],
                401
            );
        }

        return $next($request);
    }
}
