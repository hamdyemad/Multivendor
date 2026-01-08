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

    const STATUS_PENDING = 'pending';
    const STATUS_SENT_OFFER = 'sent_offer';
    const STATUS_ACCEPTED_OFFER = 'accepted_offer';
    const STATUS_REJECTED_OFFER = 'rejected_offer';
    const STATUS_ORDER_CREATED = 'order_created';
    const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
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
     * Check if offer can be sent
     */
    public function canSendOffer(): bool
    {
        return $this->status === self::STATUS_PENDING;
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

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q2) use ($search) {
                        $q2->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('customerAddress', function ($q2) use ($search) {
                        $q2->where('address', 'like', "%{$search}%");
                    });
            });
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
