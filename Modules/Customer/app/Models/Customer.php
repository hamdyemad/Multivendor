<?php

namespace Modules\Customer\app\Models;

use App\Models\Traits\HumanDates;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\AutoStoreCountryId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Modules\Customer\app\Observers\CustomerObserver;
use Modules\Order\app\Models\Order;

class Customer extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable, HasApiTokens, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        static::observe(CustomerObserver::class);
    }

    protected $guarded = [];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Modules\Customer\database\factories\CustomerFactory::new();
    }

    protected $appends = ['full_name'];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'status' => 'boolean',
        'gender' => 'string',
    ];

    // /**
    //  * Override the tokens relationship to use customer_access_tokens table
    //  */
    // public function tokens()
    // {
    //     return $this->hasMany(CustomerAccessToken::class, 'customer_id', 'id');
    // }


    /**
     * Mutator: Hash password before storing in database
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get FCM tokens for this customer
     */
    public function fcmTokens()
    {
        return $this->hasMany(CustomerFcmToken::class);
    }

    /**
     * Get addresses for this customer
     */
    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class)->latest();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get primary address
     */
    public function primaryAddress()
    {
        return $this->hasOne(CustomerAddress::class)->where('is_primary', true);
    }

    /**
     * Get country for this customer
     */
    public function country()
    {
        return $this->belongsTo(\Modules\AreaSettings\app\Models\Country::class);
    }

    /**
     * Get city for this customer
     */
    public function city()
    {
        return $this->belongsTo(\Modules\AreaSettings\app\Models\City::class);
    }

    /**
     * Get region for this customer
     */
    public function region()
    {
        return $this->belongsTo(\Modules\AreaSettings\app\Models\Region::class);
    }

    /**
     * Get vendor for this customer (if created by vendor)
     */
    public function vendor()
    {
        return $this->belongsTo(\Modules\Vendor\app\Models\Vendor::class);
    }

    /**
     * Scope to get active customers only
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope to filter customers
     */
    public function scopeFilter($query, array $filters)
    {

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Active filter
        if (isset($filters['active'])) {
            $query->where('status', $filters['active']);
        }

        // Vendor filter - For order creation, vendors should see:
        // 1. All system customers (vendor_id is NULL)
        // 2. Customers they created (vendor_id matches their vendor ID)
        if (!empty($filters['vendor_id'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereNull('vendor_id') // System customers
                  ->orWhere('vendor_id', $filters['vendor_id']); // Vendor's own customers
            });
        }

        // Date range filters
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        return $query;
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
