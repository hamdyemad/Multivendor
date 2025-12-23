<?php

namespace Modules\Order\app\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Order\database\factories\OrderFactory;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;

use Modules\Customer\app\Models\Customer;
use Modules\AreaSettings\app\Models\Country;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Region;

class Order extends BaseModel
{
    use HasFactory, HumanDates, AutoStoreCountryId,CountryCheckIdTrait;

    public static function newFactory()
    {
        return OrderFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_number',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_address',
        'customer_phone',
        'order_from',
        'payment_type',
        'customer_promo_code_title',
        'customer_promo_code_value',
        'customer_promo_code_type',
        'customer_promo_code_amount',
        'shipping',
        'total_tax',
        'total_product_price',
        'items_count',
        'total_price',
        'total_fees',
        'total_discounts',
        'stage_id',
        'city_id',
        'region_id',
        'refunded_amount',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'shipping' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_product_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'total_fees' => 'decimal:2',
        'total_discounts' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'customer_promo_code_value' => 'decimal:2',
        'customer_promo_code_amount' => 'decimal:2',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->order_number = self::generateOrderNumber();
        });
    }

    public static function generateOrderNumber()
    {
        // Get the last order number and increment it
        $lastOrder = self::orderBy('id', 'desc')->first();
        
        if ($lastOrder && $lastOrder->order_number) {
            // Extract the number from the last order number (e.g., "ORD-000103" -> 103)
            $lastNumber = (int) str_replace('ORD-', '', $lastOrder->order_number);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'ORD-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the customer associated with the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the order stage.
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(OrderStage::class);
    }

    /**
     * Get the country.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the city.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the region.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the order products.
     */
    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * Get the order fulfillments.
     */
    public function fulfillments(): HasMany
    {
        return $this->hasMany(OrderFulfillment::class);
    }

    /**
     * Get the order extra fees and discounts.
     */
    public function extraFeesDiscounts(): HasMany
    {
        return $this->hasMany(OrderExtraFeeDiscount::class);
    }

    protected function applyCustomSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('order_number', $search)
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        // Search filter
        if (!empty($filters['search'])) {
            $query->applyCustomSearch($filters['search']);
            unset($filters['search']);
        }

        // Stage filter
        if (!empty($filters['stage_id'])) {
            $query->where('stage_id', $filters['stage_id']);
        }

        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        if (!empty($filters['vendor_id'])) {
            $query->whereHas('products', function ($q) use ($filters) {
                $q->where('vendor_id', $filters['vendor_id']);
            });
        }

        return $query;
    }
}
