<?php

namespace Modules\Refund\app\Models;

use App\Models\BaseModel;
use App\Traits\HasCountries;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Order\app\Models\Order;
use Modules\Customer\app\Models\Customer;
use Modules\Vendor\app\Models\Vendor;
use Modules\AreaSettings\app\Models\Country;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
class RefundRequest extends BaseModel
{
    use SoftDeletes, HumanDates, AutoStoreCountryId,CountryCheckIdTrait;

    const STATUSES = [
        'pending' => 'refund::refund.statuses.pending',
        'approved' => 'refund::refund.statuses.approved',
        'in_progress' => 'refund::refund.statuses.in_progress',
        'picked_up' => 'refund::refund.statuses.picked_up',
        'refunded' => 'refund::refund.statuses.refunded',
        'cancelled' => 'refund::refund.statuses.cancelled',
    ];

    /**
     * Get status configurations (icon and color)
     * Keys are dynamically built from STATUSES constant
     */
    public static function getStatusConfigurations(): array
    {
        $configs = [
            'pending' => ['icon' => 'uil-clock', 'color' => 'warning'],
            'approved' => ['icon' => 'uil-check', 'color' => 'info'],
            'in_progress' => ['icon' => 'uil-sync', 'color' => 'primary'],
            'picked_up' => ['icon' => 'uil-package', 'color' => 'secondary'],
            'refunded' => ['icon' => 'uil-check-circle', 'color' => 'success'],
            'cancelled' => ['icon' => 'uil-ban', 'color' => 'danger'],
        ];

        // Only return configs for statuses that exist in STATUSES constant
        return array_intersect_key($configs, self::STATUSES);
    }

    /**
     * Get translated status label
     */
    public function getStatusLabelAttribute(): string
    {
        return trans('refund::refund.statuses.' . $this->status);
    }

    /**
     * Get all statuses with translations
     */
    public static function getTranslatedStatuses(): array
    {
        return collect(self::STATUSES)->mapWithKeys(function ($translationKey, $statusKey) {
            return [$statusKey => trans($translationKey)];
        })->toArray();
    }

    /**
     * Get status configuration (icon and color)
     */
    public static function getStatusConfig(string $status): array
    {
        $configs = self::getStatusConfigurations();
        return $configs[$status] ?? ['icon' => 'uil-redo', 'color' => 'primary'];
    }

    /**
     * Get all status configurations
     */
    public static function getAllStatusConfigs(): array
    {
        return self::getStatusConfigurations();
    }

    protected $fillable = [
        'order_id',
        'customer_id',
        'vendor_id',
        'country_id',
        'refund_number',
        'status',
        'total_products_amount',
        'total_shipping_amount',
        'total_tax_amount',
        'total_discount_amount',
        'vendor_fees_amount',
        'vendor_discounts_amount',
        'promo_code_amount',
        'return_shipping_cost',
        'customer_pays_return_shipping',
        'points_used',
        'points_to_deduct',
        'total_refund_amount',
        'reason',
        'customer_notes',
        'vendor_notes',
        'admin_notes',
        'approved_at',
        'refunded_at',
    ];

    protected $casts = [
        'total_products_amount' => 'decimal:2',
        'total_shipping_amount' => 'decimal:2',
        'total_tax_amount' => 'decimal:2',
        'total_discount_amount' => 'decimal:2',
        'vendor_fees_amount' => 'decimal:2',
        'vendor_discounts_amount' => 'decimal:2',
        'promo_code_amount' => 'decimal:2',
        'return_shipping_cost' => 'decimal:2',
        'customer_pays_return_shipping' => 'boolean',
        'points_used' => 'decimal:2',
        'points_to_deduct' => 'integer',
        'total_refund_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->refund_number = self::generateRefundNumber();
        });

        static::observe(\Modules\Refund\app\Observers\RefundRequestObserver::class);
    }

    /**
     * Generate unique refund number
     */
    public static function generateRefundNumber(): string
    {
        $date = date('Ymd');
        $lastRefund = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastRefund ? (int) substr($lastRefund->refund_number, -4) + 1 : 1;

        return 'REF-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the vendor
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Check if customer should pay return shipping
     * Uses the value saved in the refund request (snapshot at creation time)
     */
    public function shouldCustomerPayReturnShipping(): bool
    {
        return (bool) $this->customer_pays_return_shipping;
    }
    
    /**
     * Get vendor refund settings (for reference only, not used in calculations)
     */
    public function getVendorRefundSettings()
    {
        if (!$this->vendor_id) {
            return null;
        }
        
        return VendorRefundSetting::getForVendor($this->vendor_id);
    }

    /**
     * Get the refund items
     */
    public function items(): HasMany
    {
        return $this->hasMany(RefundRequestItem::class);
    }

    /**
     * Get the country
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the refund history
     */
    public function history(): HasMany
    {
        return $this->hasMany(RefundRequestHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Check if refund can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if refund can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }
    
    /**
     * Check if status can be changed
     */
    public function canChangeStatus(): bool
    {
        return !in_array($this->status, ['cancelled', 'refunded']);
    }
    
    /**
     * Get next available statuses
     */
    public function getNextStatuses(): array
    {
        $nextStatuses = [];
        
        if ($this->status === 'pending') {
            $nextStatuses = ['approved', 'cancelled'];
        } elseif ($this->status === 'approved') {
            $nextStatuses = ['in_progress'];
        } elseif ($this->status === 'in_progress') {
            $nextStatuses = ['picked_up'];
        } elseif ($this->status === 'picked_up') {
            $nextStatuses = ['refunded'];
        }
        
        return $nextStatuses;
    }
    
    /**
     * Get status background color
     */
    public function getStatusBackgroundColor(): string
    {
        $config = self::getStatusConfig($this->status);
        
        return match($config['color']) {
            'success' => '#d4edda',
            'info' => '#d1ecf1',
            'danger' => '#f8d7da',
            'warning' => '#fff3cd',
            'primary' => '#cfe2ff',
            'secondary' => '#e2e3e5',
            default => '#f8f9fa',
        };
    }
    
    /**
     * Get status text color class
     */
    public function getStatusTextColor(): string
    {
        $config = self::getStatusConfig($this->status);
        return 'text-' . $config['color'];
    }
    
    /**
     * Get status icon class
     */
    public function getStatusIcon(): string
    {
        $config = self::getStatusConfig($this->status);
        return $config['icon'];
    }
    
    /**
     * Mark refund as approved
     */
    public function markAsApproved(): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }
    
    /**
     * Mark refund as refunded
     */
    public function markAsRefunded(): void
    {
        $this->update([
            'status' => 'refunded',
            'refunded_at' => now(),
        ]);
    }

    /**
     * Calculate and update refund totals
     */
    public function calculateTotals(): void
    {
        $items = $this->items()->get();
        
        $this->total_products_amount = $items->sum('total_price');
        $this->total_tax_amount = $items->sum('tax_amount');
        $this->total_discount_amount = $items->sum('discount_amount');
        $this->total_shipping_amount = $items->sum('shipping_amount');
        
        // Calculate subtotal from items
        $subtotal = $this->total_products_amount 
            + $this->total_tax_amount 
            + $this->total_shipping_amount
            - $this->total_discount_amount;
        
        // Add vendor fees (customer should get these back)
        $subtotal += ($this->vendor_fees_amount ?? 0);
        
        // Subtract vendor discounts (customer got discount, so refund less)
        $subtotal -= ($this->vendor_discounts_amount ?? 0);
        
        // Subtract promo code amount (customer used promo, so refund less)
        $subtotal -= ($this->promo_code_amount ?? 0);
        
        // Subtract points used (customer used points, so refund less)
        $subtotal -= ($this->points_used ?? 0);
        
        // Subtract return shipping cost from refund amount
        // Note: If vendor pays return shipping, return_shipping_cost is already 0 in DB
        // If customer pays, return_shipping_cost contains the actual cost
        // So we always subtract it (either 0 or the actual cost)
        $this->total_refund_amount = $subtotal - ($this->return_shipping_cost ?? 0);
        
        $this->save();
    }

    /**
     * Scope to filter refund requests
     */
    public function scopeFilter($query, array $filters)
    {
        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by customer
        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        // Filter by vendor
        if (!empty($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Search by refund number or order number
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('refund_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('order', function ($orderQuery) use ($filters) {
                      $orderQuery->where('order_number', 'like', '%' . $filters['search'] . '%');
                  })
                  ->orWhereHas('customer', function ($customerQuery) use ($filters) {
                      $customerQuery->where('name', 'like', '%' . $filters['search'] . '%');
                  })
                  ->orWhereHas('vendor', function ($vendorQuery) use ($filters) {
                      $vendorQuery->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ['%' . $filters['search'] . '%'])
                                  ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ['%' . $filters['search'] . '%']);
                  });
            });
        }

        // Filter by vendor if not admin (for datatable)
        if (!empty($filters['current_vendor_id'])) {
            $query->where('vendor_id', $filters['current_vendor_id']);
        }

        return $query;
    }
}
