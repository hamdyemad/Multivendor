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
        'payment_visa_status',
        'payment_reference',
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
        'points_used',
        'points_cost',
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
        'points_used' => 'decimal:2',
        'points_cost' => 'decimal:2',
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
        return $this->belongsTo(OrderStage::class, 'stage_id')->withoutGlobalScope('country_filter');
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
     * Get the vendor order stages.
     */
    public function vendorOrderStages(): HasMany
    {
        return $this->hasMany(VendorOrderStage::class);
    }

    /**
     * Get the order fulfillments.
     */
    public function fulfillments(): HasMany
    {
        return $this->hasMany(OrderFulfillment::class);
    }

    /**
     * Get the vendor order stages.
     */
    public function vendorStages(): HasMany
    {
        return $this->hasMany(VendorOrderStage::class);
    }

    /**
     * Get the order extra fees and discounts.
     */
    public function extraFeesDiscounts(): HasMany
    {
        return $this->hasMany(OrderExtraFeeDiscount::class);
    }

    /**
     * Get the order payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the latest payment for the order.
     */
    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /**
     * Get the request quotation that created this order.
     */
    public function requestQuotation()
    {
        return $this->hasOne(RequestQuotation::class);
    }

    /**
     * Get total products count (sum of quantities)
     */
    public function getProductsCountAttribute(): int
    {
        return $this->products->sum('quantity');
    }

    /**
     * Get unique product stages grouped with counts
     */
    public function getProductStagesAttribute(): array
    {
        $stages = [];
        
        foreach ($this->products as $product) {
            if ($product->stage) {
                $stageKey = $product->stage_id;
                
                if (!isset($stages[$stageKey])) {
                    $stages[$stageKey] = [
                        'id' => $product->stage->id,
                        'name' => $product->stage->getTranslation('name', app()->getLocale()),
                        'color' => $product->stage->color,
                        'type' => $product->stage->type,
                        'count' => 0,
                    ];
                }
                
                $stages[$stageKey]['count']++;
            }
        }
        
        return array_values($stages);
    }

    protected function applyCustomSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        // Filter by vendor if user is a vendor (only for admin/vendor users, not customers)
        if (auth()->check() && auth()->user() instanceof \App\Models\User && method_exists(auth()->user(), 'isVendor') && auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor) {
                $query->whereHas('products', function ($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id);
                });
            }
        }

        // Search filter
        if (!empty($filters['search'])) {
            $this->applyCustomSearch($query, $filters['search']);
            unset($filters['search']);
        }

        // Stage filter - for vendors, filter by vendor_order_stages table
        if (!empty($filters['stage_id'])) {
            // Check if current user is a vendor
            $isVendorUser = auth()->check() && auth()->user()->isVendor();
            $currentVendorId = $isVendorUser ? (auth()->user()->vendor?->id ?? null) : null;
            
            if ($isVendorUser && $currentVendorId) {
                // For vendors: filter by vendor_order_stages
                $query->whereHas('vendorOrderStages', function($q) use ($filters, $currentVendorId) {
                    $q->where('vendor_id', $currentVendorId)
                      ->where('stage_id', $filters['stage_id']);
                });
            } else {
                // For admin: filter by order stage OR vendor_order_stages
                $stageId = $filters['stage_id'];
                $query->where(function($q) use ($stageId) {
                    $q->where('stage_id', $stageId)
                      ->orWhereHas('vendorOrderStages', function($subQ) use ($stageId) {
                          $subQ->where('stage_id', $stageId);
                      });
                });
            }
        }

        // Payment type filter (online / cash_on_delivery)
        if (!empty($filters['payment_type'])) {
            $query->where('payment_type', $filters['payment_type']);
        }

        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        if (!empty($filters['vendor_id'])) {
            // Handle multiple vendor IDs (comma-separated string or array)
            $vendorIds = is_array($filters['vendor_id']) 
                ? $filters['vendor_id'] 
                : explode(',', $filters['vendor_id']);
            $vendorIds = array_filter($vendorIds); // Remove empty values
            
            if (!empty($vendorIds)) {
                $query->whereHas('products', function ($q) use ($vendorIds) {
                    $q->whereIn('vendor_id', $vendorIds);
                });
            }
        }

        return $query;
    }
}
