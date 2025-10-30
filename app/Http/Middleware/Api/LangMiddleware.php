<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LangMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if($request->header('Accept-Language')){
            $locale = $request->header('Accept-Language');
            app()->setLocale($locale);
        } else {
            app()->setLocale('en');
        }
        return $next($request);
    }
}
