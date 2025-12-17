<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * HasFilterScopes Trait
 *
 * Provides common filter scopes for all models
 * Use this trait in any model that needs filtering capabilities
 */
trait HasFilterScopes
{
    /**
     * Scope: Filter by active status (simple)
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope: Search by translation
     */
    public function scopeSearch(Builder $query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->whereHas('translations', function($subQ) use ($search) {
                $subQ->where('lang_value', 'like', "%{$search}%");
            });
        });
    }

    /**
     * Scope: Filter by active status
     */
    public function scopeIsActive(Builder $query, bool $active = true)
    {
        return $query->where('active', $active);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange(Builder $query, $fromDate = null, $toDate = null)
    {
        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }
        return $query;
    }

    /**
     * Scope: Filter by vendor ID or slug
     */
    public function scopeByVendor(Builder $query, $vendorIdentifier)
    {
        return $query->whereHas('vendor', function($subQ) use ($vendorIdentifier) {
            $subQ->where('id', $vendorIdentifier)
                ->orWhere('slug', $vendorIdentifier);
        });
    }

    /**
     * Scope: Filter by brand ID or slug
     */
    public function scopeByBrand(Builder $query, $brandIdentifier)
    {
        return $query->whereHas('brand', function ($subQ) use ($brandIdentifier) {
            $subQ->where('id', $brandIdentifier)
                ->orWhere('slug', $brandIdentifier);
        });
    }

    /**
     * Scope: Filter by department ID or slug
     */
    public function scopeByDepartment(Builder $query, $departmentIdentifier)
    {
        return $query->whereHas('department', function ($subQ) use ($departmentIdentifier) {
            $subQ->where('id', $departmentIdentifier)
                ->orWhere('slug', $departmentIdentifier);
        });
    }

    /**
     * Scope: Filter by category ID or slug
     */
    public function scopeByCategory(Builder $query, $categoryIdentifier)
    {
        return $query->whereHas('category', function ($subQ) use ($categoryIdentifier) {
            $subQ->where('id', $categoryIdentifier)
                ->orWhere('slug', $categoryIdentifier);
        });
    }

    /**
     * Scope: Filter by sub-category ID or slug
     */
    public function scopeBySubCategory(Builder $query, $subCategoryIdentifier)
    {
        return $query->whereHas('subCategory', function ($subQ) use ($subCategoryIdentifier) {
            $subQ->where('id', $subCategoryIdentifier)
                ->orWhere('slug', $subCategoryIdentifier);
        });
    }

    /**
     * Scope: Filter by country ID or slug
     */
    public function scopeByCountry(Builder $query, $countryIdentifier)
    {
        return $query->whereHas('country', function ($subQ) use ($countryIdentifier) {
            $subQ->where('id', $countryIdentifier)
                ->orWhere('slug', $countryIdentifier);
        });
    }

    /**
     * Scope: Filter by city ID or slug
     */
    public function scopeByCity(Builder $query, $cityIdentifier)
    {
        return $query->whereHas('city', function ($subQ) use ($cityIdentifier) {
            $subQ->where('id', $cityIdentifier)
                ->orWhere('slug', $cityIdentifier);
        });
    }

    /**
     * Scope: Filter by region ID or slug
     */
    public function scopeByRegion(Builder $query, $regionIdentifier)
    {
        return $query->whereHas('region', function ($subQ) use ($regionIdentifier) {
            $subQ->where('id', $regionIdentifier)
                ->orWhere('slug', $regionIdentifier);
        });
    }

    /**
     * Scope: Filter by sub-region ID or slug
     */
    public function scopeBySubRegion(Builder $query, $subRegionIdentifier)
    {
        return $query->whereHas('subRegion', function ($subQ) use ($subRegionIdentifier) {
            $subQ->where('id', $subRegionIdentifier)
                ->orWhere('slug', $subRegionIdentifier);
        });
    }

    /**
     * Apply custom search logic for the model
     * Override in child models to add custom search fields
     */
    protected function applyCustomSearch(Builder $query, string $search): Builder
    {
        return $query;
    }

    /**
     * Scope: Main filter scope that combines all small scopes
     * Can be overridden in child models to add custom logic
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                // Apply default search
                $q->whereHas('translations', function($subQ) use ($search) {
                    $subQ->where('lang_value', 'like', "%{$search}%");
                });

                // Apply custom search logic from child model
                $this->applyCustomSearch($q, $search);
            });
        }

        if(!empty($filters['char']))
        {
            $query->whereHas('translations', function($subQ) use ($filters) {
                $subQ->where('lang_value', 'like', "{$filters['char']}%");
            });
        }

        // Active filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->isActive($filters['active']);
        }

        // Date range filter
        if (!empty($filters['created_date_from']) || !empty($filters['created_date_to'])) {
            $query->dateRange(
                $filters['created_date_from'] ?? null,
                $filters['created_date_to'] ?? null
            );
        }


        // Vendor filter
        if (!empty($filters['vendor_id'])) {
            $query->byVendor($filters['vendor_id']);
        }

        // Brand filter
        if (!empty($filters['brand_id'])) {
            $query->byBrand($filters['brand_id']);
        }

        // Department filter
        if (!empty($filters['department_id'])) {
            $query->byDepartment($filters['department_id']);
        }

        // Category filter
        if (!empty($filters['category_id'])) {
            $query->byCategory($filters['category_id']);
        }

        // Sub-category filter
        if (!empty($filters['sub_category_id'])) {
            $query->bySubCategory($filters['sub_category_id']);
        }

        // Country filter
        if (!empty($filters['country_id'])) {
            $query->byCountry($filters['country_id']);
        }

        // City filter
        if (!empty($filters['city_id'])) {
            $query->byCity($filters['city_id']);
        }

        // Region filter
        if (!empty($filters['region_id'])) {
            $query->byRegion($filters['region_id']);
        }

        // Sub-region filter
        if (!empty($filters['sub_region_id'])) {
            $query->bySubRegion($filters['sub_region_id']);
        }

        if (!empty($filters['limit'])) {
            $query->limit($filters['limit']);
        }

        return $query;
    }
}
