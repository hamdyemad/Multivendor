<?php

namespace Modules\Refund\app\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Order\app\Models\Order;
use Modules\Customer\app\Models\Customer;
use Modules\Vendor\app\Models\Vendor;

class RefundRequest extends BaseModel
{
    use SoftDeletes;

    const STATUSES = [
        'pending' => 'refund::refund.statuses.pending',
        'approved' => 'refund::refund.statuses.approved',
        'in_progress' => 'refund::refund.statuses.in_progress',
        'picked_up' => 'refund::refund.statuses.picked_up',
        'refunded' => 'refund::refund.statuses.refunded',
        'rejected' => 'refund::refund.statuses.rejected',
        'cancelled' => 'refund::refund.statuses.cancelled',
    ];

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

    protected $fillable = [
        'parent_refund_id',
        'is_parent',
        'order_id',
        'customer_id',
        'vendor_id',
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
        'is_parent' => 'boolean',
        'total_products_amount' => 'decimal:2',
        'total_shipping_amount' => 'decimal:2',
        'total_tax_amount' => 'decimal:2',
        'total_discount_amount' => 'decimal:2',
        'vendor_fees_amount' => 'decimal:2',
        'vendor_discounts_amount' => 'decimal:2',
        'promo_code_amount' => 'decimal:2',
        'return_shipping_cost' => 'decimal:2',
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
     * Get the refund items
     */
    public function items(): HasMany
    {
        return $this->hasMany(RefundRequestItem::class);
    }

    /**
     * Get the parent refund request (for vendor refunds)
     */
    public function parentRefund(): BelongsTo
    {
        return $this->belongsTo(RefundRequest::class, 'parent_refund_id');
    }

    /**
     * Get the child vendor refund requests (for customer refunds)
     */
    public function vendorRefunds(): HasMany
    {
        return $this->hasMany(RefundRequest::class, 'parent_refund_id');
    }

    /**
     * Scope to get only parent (customer) refunds
     */
    public function scopeParentOnly($query)
    {
        return $query->where('is_parent', true);
    }

    /**
     * Scope to get only vendor refunds
     */
    public function scopeVendorOnly($query)
    {
        return $query->where('is_parent', false);
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
     * Check if refund can be rejected
     */
    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
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
        
        // Calculate final refund amount
        $this->total_refund_amount = $this->total_products_amount 
            + $this->total_tax_amount 
            + $this->total_shipping_amount
            - $this->total_discount_amount
            - ($this->return_shipping_cost ?? 0);
        
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
