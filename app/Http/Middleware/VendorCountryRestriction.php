<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorCountryRestriction
{
    /**
     * Handle an incoming request.
     * 
     * Restricts vendors to only access their assigned country.
     * If a vendor tries to access a different country via URL, redirect them to their country.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Only apply restriction to vendors
        if (!$user || !isVendor()) {
            return $next($request);
        }
        
        // Get vendor (either owned vendor or assigned vendor)
        $vendor = $user->vendorByUser ?? $user->vendorById;
        
        if (!$vendor || !$vendor->country) {
            return $next($request);
        }
        
        // Get country code from URL
        $urlCountryCode = strtoupper($request->route('countryCode') ?? '');
        
        // Get vendor's country code
        $vendorCountryCode = strtoupper($vendor->country->code ?? '');
        
        // If URL country doesn't match vendor's country, redirect to vendor's country
        if ($urlCountryCode && $vendorCountryCode && $urlCountryCode !== $vendorCountryCode) {
            // Get current route name and parameters
            $routeName = $request->route()->getName();
            $routeParams = $request->route()->parameters();
            
            // Replace country code with vendor's country code
            $routeParams['countryCode'] = strtolower($vendorCountryCode);
            
            // Redirect to the same route but with vendor's country
            return redirect()->route($routeName, $routeParams)
                ->with('warning', __('common.vendor_country_restriction'));
        }
        
        return $next($request);
    }
}
