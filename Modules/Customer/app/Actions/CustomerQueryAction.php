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
        $query = Customer::query();

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
        $query->with(['addresses', 'fcmTokens']);

        // Order by latest first
        $query->latest();

        return $query;
    }
}
