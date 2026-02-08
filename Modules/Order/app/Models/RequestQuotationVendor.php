<?php

namespace Modules\Order\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Vendor\app\Models\Vendor;

class RequestQuotationVendor extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_OFFER_SENT = 'offer_sent';
    const STATUS_OFFER_ACCEPTED = 'offer_accepted';
    const STATUS_OFFER_REJECTED = 'offer_rejected';
    const STATUS_ORDER_CREATED = 'order_created';

    protected $fillable = [
        'request_quotation_id',
        'vendor_id',
        'status',
        'offer_price',
        'offer_notes',
        'offer_sent_at',
        'offer_responded_at',
        'order_id',
    ];

    protected $casts = [
        'offer_price' => 'decimal:2',
        'offer_sent_at' => 'datetime',
        'offer_responded_at' => 'datetime',
    ];

    /**
     * Get the request quotation
     */
    public function requestQuotation(): BelongsTo
    {
        return $this->belongsTo(RequestQuotation::class);
    }

    /**
     * Get the vendor
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the order (if created)
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if vendor can send offer
     */
    public function canSendOffer(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if customer can respond to offer
     */
    public function canRespondToOffer(): bool
    {
        return $this->status === self::STATUS_OFFER_SENT;
    }

    /**
     * Send offer from vendor
     */
    public function sendOffer(float $price, ?string $notes = null): void
    {
        if (!$this->canSendOffer()) {
            throw new \Exception('Cannot send offer in current status');
        }

        $this->update([
            'status' => self::STATUS_OFFER_SENT,
            'offer_price' => $price,
            'offer_notes' => $notes,
            'offer_sent_at' => now(),
        ]);

        // Update parent request quotation status
        $this->updateParentStatus();
    }

    /**
     * Accept offer from customer
     */
    public function acceptOffer(): void
    {
        if (!$this->canRespondToOffer()) {
            throw new \Exception('Cannot accept offer in current status');
        }

        $this->update([
            'status' => self::STATUS_OFFER_ACCEPTED,
            'offer_responded_at' => now(),
        ]);

        // Update parent request quotation status
        $this->updateParentStatus();
    }

    /**
     * Reject offer from customer
     */
    public function rejectOffer(): void
    {
        if (!$this->canRespondToOffer()) {
            throw new \Exception('Cannot reject offer in current status');
        }

        $this->update([
            'status' => self::STATUS_OFFER_REJECTED,
            'offer_responded_at' => now(),
        ]);

        // Update parent request quotation status
        $this->updateParentStatus();
    }

    /**
     * Mark as order created
     */
    public function markOrderCreated(int $orderId): void
    {
        $this->update([
            'status' => self::STATUS_ORDER_CREATED,
            'order_id' => $orderId,
        ]);

        // Update parent request quotation status
        $this->updateParentStatus();
    }

    /**
     * Update parent request quotation status based on vendors statuses
     */
    protected function updateParentStatus(): void
    {
        $quotation = $this->requestQuotation;
        $vendors = $quotation->vendors;

        // Count statuses
        $totalVendors = $vendors->count();
        $pendingCount = $vendors->where('status', self::STATUS_PENDING)->count();
        $offerSentCount = $vendors->where('status', self::STATUS_OFFER_SENT)->count();
        $acceptedCount = $vendors->where('status', self::STATUS_OFFER_ACCEPTED)->count();
        $rejectedCount = $vendors->where('status', self::STATUS_OFFER_REJECTED)->count();
        $orderCreatedCount = $vendors->where('status', self::STATUS_ORDER_CREATED)->count();

        // Determine new status
        $newStatus = null;

        if ($orderCreatedCount > 0) {
            // At least one order created
            $newStatus = RequestQuotation::STATUS_ORDERS_CREATED;
        } elseif ($acceptedCount > 0 && $acceptedCount < $totalVendors) {
            // Some offers accepted but not all
            $newStatus = RequestQuotation::STATUS_PARTIALLY_ACCEPTED;
        } elseif ($acceptedCount === $totalVendors) {
            // All offers accepted
            $newStatus = RequestQuotation::STATUS_FULLY_ACCEPTED;
        } elseif ($rejectedCount === $totalVendors) {
            // All offers rejected
            $newStatus = RequestQuotation::STATUS_REJECTED;
        } elseif ($offerSentCount > 0 || $acceptedCount > 0 || $rejectedCount > 0) {
            // At least one offer sent/responded
            $newStatus = RequestQuotation::STATUS_OFFERS_RECEIVED;
        } elseif ($pendingCount === $totalVendors) {
            // All vendors still pending
            $newStatus = RequestQuotation::STATUS_SENT_TO_VENDORS;
        }

        if ($newStatus && $quotation->status !== $newStatus) {
            $quotation->update(['status' => $newStatus]);
        }
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => __('order::request-quotation.vendor_status_pending'),
            self::STATUS_OFFER_SENT => __('order::request-quotation.vendor_status_offer_sent'),
            self::STATUS_OFFER_ACCEPTED => __('order::request-quotation.vendor_status_offer_accepted'),
            self::STATUS_OFFER_REJECTED => __('order::request-quotation.vendor_status_offer_rejected'),
            self::STATUS_ORDER_CREATED => __('order::request-quotation.vendor_status_order_created'),
            default => $this->status,
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_OFFER_SENT => 'info',
            self::STATUS_OFFER_ACCEPTED => 'success',
            self::STATUS_OFFER_REJECTED => 'danger',
            self::STATUS_ORDER_CREATED => 'primary',
            default => 'secondary',
        };
    }

    /**
     * Scope for searching vendor quotations
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->whereHas('requestQuotation', function ($q) use ($search) {
            $q->where('notes', 'like', "%{$search}%")
                ->orWhere('quotation_number', 'like', "%{$search}%")
                ->orWhereHas('customer', function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus(Builder $query, ?string $status): Builder
    {
        if (empty($status) || $status === 'all') {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by vendor
     */
    public function scopeForVendor(Builder $query, int $vendorId): Builder
    {
        return $query->where('vendor_id', $vendorId);
    }

    /**
     * Scope for filtering with multiple criteria
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (!empty($filters['vendor_id'])) {
            $query->forVendor($filters['vendor_id']);
        }

        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        return $query;
    }
}
