<?php

namespace Modules\Customer\app\Actions;

use Modules\Customer\app\Models\Customer;

class CustomerQueryAction
{
    /**
     * Build the query with filters
     */
    public function handle(array $filters = [])
    {
        $query = Customer::query()->with('country');

        // Filter by vendor if user is not admin - include vendor's customers AND customers with no vendor
        if (!isAdmin() && auth()->check() && auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor) {
                $query->where(function ($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id)
                      ->orWhereNull('vendor_id');
                });
            }
        }

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status filter (handle both 'status' and 'active' parameters)
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('status', $filters['active']);
        }

        // City filter
        if (!empty($filters['city_id'])) {
            $query->where('city_id', intval($filters['city_id']));
        }

        // Region filter
        if (!empty($filters['region_id'])) {
            $query->where('region_id', intval($filters['region_id']));
        }

        // Vendor filter (for admins only)
        if (isAdmin() && !empty($filters['vendor_id'])) {
            $query->where('vendor_id', intval($filters['vendor_id']));
        }

        // Email verification filter
        if (isset($filters['email_verified']) && $filters['email_verified'] !== '') {
            if ($filters['email_verified'] == '1') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Language filter
        if (!empty($filters['lang'])) {
            $query->where('lang', $filters['lang']);
        }

        // Date range filters (handle both old and new parameter names)
        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        // Load relationships
        $query->with([
        'addresses.country', 
        'addresses.city',
        'addresses.region',
        'addresses.subregion',
        'fcmTokens',
        'city',
        'region']);

        // Order by latest first
        $query->latest();

        return $query;
    }
}
