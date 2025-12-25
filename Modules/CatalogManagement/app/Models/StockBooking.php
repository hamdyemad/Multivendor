<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderProduct;
use Modules\AreaSettings\app\Models\Region;

class StockBooking extends BaseModel
{
    use HasFactory, HumanDates;

    const STATUS_BOOKED = 'booked';
    const STATUS_ALLOCATED = 'allocated';
    const STATUS_RELEASED = 'released';
    const STATUS_FULFILLED = 'fulfilled';

    protected $fillable = [
        'order_id',
        'order_product_id',
        'vendor_product_variant_id',
        'region_id',
        'allocated_region_id',
        'booked_quantity',
        'status',
        'booked_at',
        'allocated_at',
        'released_at',
        'fulfilled_at',
    ];

    /**
     * Get the order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the order product
     */
    public function orderProduct(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class);
    }

    /**
     * Get the vendor product variant
     */
    public function vendorProductVariant(): BelongsTo
    {
        return $this->belongsTo(VendorProductVariant::class);
    }

    /**
     * Get the region
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the allocated region
     */
    public function allocatedRegion(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'allocated_region_id');
    }

    /**
     * Scope for booked status
     */
    public function scopeBooked($query)
    {
        return $query->where('status', self::STATUS_BOOKED);
    }

    /**
     * Scope for allocated status
     */
    public function scopeAllocated($query)
    {
        return $query->where('status', self::STATUS_ALLOCATED);
    }

    /**
     * Scope for released status
     */
    public function scopeReleased($query)
    {
        return $query->where('status', self::STATUS_RELEASED);
    }

    /**
     * Scope for fulfilled status
     */
    public function scopeFulfilled($query)
    {
        return $query->where('status', self::STATUS_FULFILLED);
    }

    /**
     * Book stock for an order
     */
    public static function bookStock(int $orderId, int $orderProductId, int $variantId, int $regionId, int $quantity): self
    {
        return self::create([
            'order_id' => $orderId,
            'order_product_id' => $orderProductId,
            'vendor_product_variant_id' => $variantId,
            'region_id' => $regionId,
            'booked_quantity' => $quantity,
            'status' => self::STATUS_BOOKED,
            'booked_at' => now(),
        ]);
    }

    /**
     * Release booked stock (when order is canceled)
     */
    public function release(): bool
    {
        return $this->update([
            'status' => self::STATUS_RELEASED,
            'released_at' => now(),
        ]);
    }

    /**
     * Mark as allocated (when stock is allocated to a region)
     */
    public function allocate(int $regionId): bool
    {
        return $this->update([
            'allocated_region_id' => $regionId,
            'status' => self::STATUS_ALLOCATED,
            'allocated_at' => now(),
        ]);
    }

    /**
     * Mark as fulfilled (when order is delivered)
     */
    public function fulfill(): bool
    {
        return $this->update([
            'status' => self::STATUS_FULFILLED,
            'fulfilled_at' => now(),
        ]);
    }
}
