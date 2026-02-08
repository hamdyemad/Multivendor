<?php

namespace Modules\Order\app\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use Modules\AreaSettings\app\Models\Country;
use Modules\Customer\app\Models\Customer;
use Modules\Customer\app\Models\CustomerAddress;

class RequestQuotation extends BaseModel
{
    use HasFactory, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->quotation_number = self::generateQuotationNumber();
        });
    }

    /**
     * Generate a unique quotation number with database locking to prevent race conditions.
     * Uses pessimistic locking and retry mechanism for concurrent requests.
     */
    public static function generateQuotationNumber(): string
    {
        $maxRetries = 5;
        $attempt = 0;
        
        while ($attempt < $maxRetries) {
            try {
                return \Illuminate\Support\Facades\DB::transaction(function () {
                    // Lock the request_quotations table for update to prevent race conditions
                    $lastQuotation = self::query()
                        ->lockForUpdate()
                        ->orderBy('id', 'desc')
                        ->first();
                    
                    if ($lastQuotation && $lastQuotation->quotation_number) {
                        // Extract the number from the last quotation number (e.g., "RQ-000103" -> 103)
                        $lastNumber = (int) str_replace('RQ-', '', $lastQuotation->quotation_number);
                        $nextNumber = $lastNumber + 1;
                    } else {
                        $nextNumber = 1;
                    }
                    
                    $quotationNumber = 'RQ-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
                    
                    // Double-check uniqueness before returning
                    if (self::where('quotation_number', $quotationNumber)->exists()) {
                        throw new \Exception('Duplicate quotation number detected');
                    }
                    
                    return $quotationNumber;
                }, 5);
            } catch (\Exception $e) {
                $attempt++;
                if ($attempt >= $maxRetries) {
                    // Fallback: use timestamp-based number
                    return 'RQ-' . time() . rand(100, 999);
                }
                usleep(100000); // Wait 100ms before retry
            }
        }
        
        // Final fallback
        return 'RQ-' . time() . rand(100, 999);
    }

    // Old statuses (kept for backward compatibility)
    const STATUS_PENDING = 'pending';
    const STATUS_SENT_OFFER = 'sent_offer';
    const STATUS_ACCEPTED_OFFER = 'accepted_offer';
    const STATUS_REJECTED_OFFER = 'rejected_offer';
    const STATUS_ORDER_CREATED = 'order_created';
    const STATUS_ARCHIVED = 'archived';
    
    // New multi-vendor statuses
    const STATUS_SENT_TO_VENDORS = 'sent_to_vendors';
    const STATUS_OFFERS_RECEIVED = 'offers_received';
    const STATUS_PARTIALLY_ACCEPTED = 'partially_accepted';
    const STATUS_FULLY_ACCEPTED = 'fully_accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ORDERS_CREATED = 'orders_created';

    protected $fillable = [
        'quotation_number',
        'notes',
        'file',
        'offer_sent_at',
        'offer_responded_at',
        'status',
        'country_id',
        'customer_id',
        'customer_address_id',
        'order_id',
    ];

    protected $casts = [
        'offer_sent_at' => 'datetime',
        'offer_responded_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class)->withTrashed();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the vendors assigned to this quotation (many-to-many through pivot)
     */
    public function vendors()
    {
        return $this->hasMany(RequestQuotationVendor::class);
    }

    /**
     * Get the orders created from this quotation
     */
    public function orders()
    {
        return $this->hasManyThrough(
            Order::class,
            RequestQuotationVendor::class,
            'request_quotation_id',
            'id',
            'id',
            'order_id'
        )->whereNotNull('request_quotation_vendors.order_id');
    }

    /**
     * Get customer name from customer
     */
    public function getCustomerNameAttribute(): ?string
    {
        return $this->customer?->full_name;
    }

    /**
     * Get customer email from customer
     */
    public function getCustomerEmailAttribute(): ?string
    {
        return $this->customer?->email;
    }

    /**
     * Get customer phone from customer
     */
    public function getCustomerPhoneAttribute(): ?string
    {
        return $this->customer?->phone;
    }

    /**
     * Get full address string from customer address
     */
    public function getFullAddressAttribute(): ?string
    {
        if (!$this->customerAddress) {
            return null;
        }
        
        $parts = [];
        if ($this->customerAddress->address) {
            $parts[] = $this->customerAddress->address;
        }
        if ($this->customerAddress->subregion) {
            $parts[] = $this->customerAddress->subregion->name;
        }
        if ($this->customerAddress->region) {
            $parts[] = $this->customerAddress->region->name;
        }
        if ($this->customerAddress->city) {
            $parts[] = $this->customerAddress->city->name;
        }
        if ($this->customerAddress->country) {
            $parts[] = $this->customerAddress->country->name;
        }
        return implode(', ', $parts);
    }

    /**
     * Check if offer can be sent (old single-vendor workflow)
     */
    public function canSendOffer(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if vendors can be selected (new multi-vendor workflow)
     */
    public function canSendToVendors(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if has any vendors assigned
     */
    public function hasVendors(): bool
    {
        return $this->vendors()->exists();
    }

    /**
     * Check if all vendors have sent offers
     */
    public function allVendorsSentOffers(): bool
    {
        $totalVendors = $this->vendors()->count();
        if ($totalVendors === 0) {
            return false;
        }
        
        $sentOffers = $this->vendors()
            ->whereIn('status', [
                RequestQuotationVendor::STATUS_OFFER_SENT,
                RequestQuotationVendor::STATUS_OFFER_ACCEPTED,
                RequestQuotationVendor::STATUS_OFFER_REJECTED,
                RequestQuotationVendor::STATUS_ORDER_CREATED,
            ])
            ->count();
        
        return $sentOffers === $totalVendors;
    }

    /**
     * Get pending vendors (haven't sent offer yet)
     */
    public function getPendingVendorsAttribute()
    {
        return $this->vendors()
            ->where('status', RequestQuotationVendor::STATUS_PENDING)
            ->with('vendor')
            ->get();
    }

    /**
     * Get vendors with offers sent
     */
    public function getOffersAttribute()
    {
        return $this->vendors()
            ->whereIn('status', [
                RequestQuotationVendor::STATUS_OFFER_SENT,
                RequestQuotationVendor::STATUS_OFFER_ACCEPTED,
                RequestQuotationVendor::STATUS_OFFER_REJECTED,
                RequestQuotationVendor::STATUS_ORDER_CREATED,
            ])
            ->with('vendor', 'order')
            ->get();
    }

    /**
     * Check if offer can be responded to (accept/reject)
     */
    public function canRespondToOffer(): bool
    {
        return $this->status === self::STATUS_SENT_OFFER;
    }

    /**
     * Check if order can be assigned
     */
    public function canAssignOrder(): bool
    {
        return $this->status === self::STATUS_ACCEPTED_OFFER && !$this->order_id;
    }

    /**
     * Scope for searching quotations
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('notes', 'like', "%{$search}%")
                ->orWhere('quotation_number', 'like', "%{$search}%")
                ->orWhereHas('customer', function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('customerAddress', function ($q2) use ($search) {
                    $q2->where('address', 'like', "%{$search}%");
                })
                ->orWhereHas('vendors.order', function ($q2) use ($search) {
                    $q2->where('order_number', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Scope for filtering by vendor status
     */
    public function scopeVendorStatus(Builder $query, ?string $status): Builder
    {
        if (empty($status)) {
            return $query;
        }

        return $query->whereHas('vendors', function ($q) use ($status) {
            $q->where('status', $status);
        });
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['vendor_status'])) {
            $query->vendorStatus($filters['vendor_status']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        return $query;
    }

    public function scopeNotArchived(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_ARCHIVED);
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }
}
