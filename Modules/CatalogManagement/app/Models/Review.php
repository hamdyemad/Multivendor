<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\Customer\app\Models\Customer;
use App\Models\Traits\HumanDates;
use App\Models\Traits\CountryCheckIdTrait;

class Review extends BaseModel
{
    use HasFactory, SoftDeletes, HumanDates, CountryCheckIdTrait;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Modules\CatalogManagement\database\factories\ReviewFactory::new();
    }

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $table = 'reviews';

    protected $fillable = [
        'reviewable_id',
        'reviewable_type',
        'customer_id',
        'review',
        'star',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'star' => 'integer',
    ];

    /**
     * Get all available status values
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => __('common.pending'),
            self::STATUS_APPROVED => __('common.approved'),
            self::STATUS_REJECTED => __('common.rejected'),
        ];
    }

    /**
     * Get the vendor product that owns this review
     */
    public function vendorProduct()
    {
        return $this->belongsTo(VendorProduct::class);
    }

    /**
     * Get the customer who wrote this review
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Scope: Get approved reviews only
     */
    public function scopeApproved(Builder $query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope: Get pending reviews only
     */
    public function scopePending(Builder $query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: Get rejected reviews only
     */
    public function scopeRejected(Builder $query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus(Builder $query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by vendor product
     */
    public function scopeByVendorProduct(Builder $query, int $vendorProductId)
    {
        return $query->where('vendor_product_id', $vendorProductId);
    }

    /**
     * Scope: Filter by customer
     */
    public function scopeByCustomer(Builder $query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope: Filter by star rating
     */
    public function scopeByStar(Builder $query, int $star)
    {
        return $query->where('star', $star);
    }

    /**
     * Scope: Filter by minimum star rating
     */
    public function scopeByMinStar(Builder $query, int $minStar)
    {
        return $query->where('star', '>=', $minStar);
    }

    /**
     * Scope: Filter by maximum star rating
     */
    public function scopeByMaxStar(Builder $query, int $maxStar)
    {
        return $query->where('star', '<=', $maxStar);
    }

    public function scopeByReviewable(Builder $query, int $reviewableId, string $reviewableType)
    {
        return $query->where('reviewable_id', $reviewableId)->where('reviewable_type', $reviewableType);
    }

    public function scopeByReviewableType(Builder $query, string $reviewableType)
    {
        return $query->where('reviewable_type', $reviewableType);
    }

    /**
     * Scope: Filter reviews
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        parent::scopeFilter($query, $filters);

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (!empty($filters['reviewable_id']) && !empty($filters['reviewable_type'])) {
            $query->byReviewable($filters['reviewable_id'], $filters['reviewable_type']);
        }

        if (!empty($filters['reviewable_type'])) {
            $query->byReviewableType($filters['reviewable_type']);
        }

        if (!empty($filters['customer_id'])) {
            $query->byCustomer($filters['customer_id']);
        }

        if (!empty($filters['star'])) {
            $query->byStar($filters['star']);
        }

        if (!empty($filters['min_star'])) {
            $query->byMinStar($filters['min_star']);
        }

        if (!empty($filters['max_star'])) {
            $query->byMaxStar($filters['max_star']);
        }

        return $query;
    }
}

