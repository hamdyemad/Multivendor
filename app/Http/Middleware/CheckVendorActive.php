<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckVendorActive
{
    /**
     * Handle an incoming request.
     * Check if the authenticated user is a vendor and if their vendor account is active.
     * If not active, logout and redirect with error message.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Only check for authenticated users who are vendors
        if ($user && isVendor()) {
            // Get vendor (either owned vendor or assigned vendor)
            $vendor = $user->vendorByUser ?? $user->vendorById;
            
            if ($vendor && !$vendor->active) {
                // Logout the user
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Redirect to login with error message
                return redirect()->route('login')
                    ->with('message', __('vendor::vendor.vendor_account_inactive'));
            }
        }
        
        return $next($request);
    }
}
