<?php

namespace Modules\Customer\app\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Country;
use Modules\AreaSettings\app\Models\Region;
use Modules\AreaSettings\app\Models\Subregion;

class CustomerAddress extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_addresses';

    protected $guarded = [];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the customer that owns this address
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class)->withTrashed();
    }

    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }

    public function region()
    {
        return $this->belongsTo(Region::class)->withTrashed();
    }

    public function subregion()
    {
        return $this->belongsTo(Subregion::class)->withTrashed();
    }

    // Scopes

    /**
     * Apply custom search logic for CustomerAddress
     * Searches in title, address, and location names
     */
    protected function applyCustomSearch(Builder $query, string $search): Builder
    {
        return $query->where('title', 'like', "%{$search}%")
            ->orWhere('address', 'like', "%{$search}%")
            ->orWhereHas('country', function ($subQ) use ($search) {
                $subQ->whereHas('translations', function ($subSubQ) use ($search) {
                    $subSubQ->where('lang_value', 'like', "%{$search}%");
                });
            })
            ->orWhereHas('city', function ($subQ) use ($search) {
                $subQ->whereHas('translations', function ($subSubQ) use ($search) {
                    $subSubQ->where('lang_value', 'like', "%{$search}%");
                });
            })
            ->orWhereHas('region', function ($subQ) use ($search) {
                $subQ->whereHas('translations', function ($subSubQ) use ($search) {
                    $subSubQ->where('lang_value', 'like', "%{$search}%");
                });
            })
            ->orWhereHas('subregion', function ($subQ) use ($search) {
                $subQ->whereHas('translations', function ($subSubQ) use ($search) {
                    $subSubQ->where('lang_value', 'like', "%{$search}%");
                });
            });
    }

    /**
     * Filter scope for address queries
     * Handles search and address-specific filters
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        if (!empty($filters['search'])) {
            $this->applyCustomSearch($query, $filters['search']);
        }

        // Filter by country
        if (!empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        // Filter by city
        if (!empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        // Filter by region
        if (!empty($filters['region_id'])) {
            $query->where('region_id', $filters['region_id']);
        }

        // Filter by subregion
        if (!empty($filters['subregion_id'])) {
            $query->where('subregion_id', $filters['subregion_id']);
        }

        // Filter by primary status
        if (isset($filters['is_primary']) && $filters['is_primary'] !== null) {
            $query->where('is_primary', $filters['is_primary']);
        }

        // Filter by customer
        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        return $query;
    }
}
