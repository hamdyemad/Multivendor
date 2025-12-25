<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Traits\HumanDates;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Modules\Order\app\Models\OrderFulfillment;
use Modules\Order\app\Models\OrderProduct;

class VendorProductVariant extends Model
{
    use HasFactory, SoftDeletes, HumanDates;

    protected $table = 'vendor_product_variants';
    protected $guarded = [];

    protected $with = ['stocks'];

    protected $casts = [
        'price' => 'decimal:2',
        'has_discount' => 'boolean',
        'price_before_discount' => 'decimal:2',
        'discount_end_date' => 'date',
    ];

    protected $appends = ['total_stock', 'variant_name', 'discount', 'quantity_in_cart', 'cart_id', 'countDeliveredProduct', 'countOfAvailable', 'variant_path_en', 'variant_path_ar'];

    public function getHasDiscountAttribute()
    {
        return $this->attributes['has_discount'] ?? false;
    }

    public function setHasDiscountAttribute($value)
    {
        $this->attributes['has_discount'] = $value;
    }

    public function vendorProduct()
    {
        return $this->belongsTo(VendorProduct::class);
    }

    public function variantConfiguration()
    {
        return $this->belongsTo(VariantsConfiguration::class, 'variant_configuration_id');
    }

    public function stocks()
    {
        return $this->hasMany(VendorProductVariantStock::class);
    }

    /**
     * Get stock bookings for this variant
     */
    public function stockBookings()
    {
        return $this->hasMany(StockBooking::class);
    }

    /**
     * Get active (booked) stock bookings
     */
    public function activeBookings()
    {
        return $this->stockBookings()->where('status', StockBooking::STATUS_BOOKED);
    }

    public function fulfillments()
    {
        return $this->hasManyThrough(
            OrderFulfillment::class,
            OrderProduct::class,
            'vendor_product_variant_id',
            'order_product_id'
        );
    }

    public function getEffectivePrice()
    {
        if ($this->has_discount && $this->discount_end_date && $this->discount_end_date->isFuture()) {
            return $this->price_before_discount;
        }

        return $this->price;
    }

    public function isDiscountValid()
    {
        return $this->has_discount && $this->discount_end_date && $this->discount_end_date->isFuture();
    }

    public function getTotalStock()
    {
        return $this->stocks()->sum('quantity');
    }

    public function getTotalStockAttribute()
    {
        return $this->stocks->sum('quantity') ?? 0;
    }

    /**
     * Get total booked stock (orders pending/processing)
     * Not in appends to avoid N+1 - call explicitly when needed
     */
    public function getBookedStockAttribute()
    {
        return (int) StockBooking::where('vendor_product_variant_id', $this->id)
            ->where('status', StockBooking::STATUS_BOOKED)
            ->sum('booked_quantity');
    }

    /**
     * Get total allocated stock
     * Not in appends to avoid N+1 - call explicitly when needed
     */
    public function getAllocatedStockAttribute()
    {
        return (int) StockBooking::where('vendor_product_variant_id', $this->id)
            ->where('status', StockBooking::STATUS_ALLOCATED)
            ->sum('booked_quantity');
    }

    /**
     * Get total fulfilled stock (delivered orders)
     * Not in appends to avoid N+1 - call explicitly when needed
     */
    public function getFulfilledStockAttribute()
    {
        return (int) StockBooking::where('vendor_product_variant_id', $this->id)
            ->where('status', StockBooking::STATUS_FULFILLED)
            ->sum('booked_quantity');
    }

    /**
     * Get remaining stock (total - booked - allocated - fulfilled)
     * Not in appends to avoid N+1 - call explicitly when needed
     */
    public function getRemainingStockAttribute()
    {
        return max(0, $this->total_stock - $this->booked_stock - $this->allocated_stock - $this->fulfilled_stock);
    }

    public function getVariantNameAttribute()
    {
        return $this->variantConfiguration->name ?? '--';
    }

    public function getVariantPathEnAttribute()
    {
        if (!$this->variantConfiguration) {
            return '';
        }

        $path = [];
        $current = $this->variantConfiguration;

        $path[] = $current->getTranslation('color', 'en') ?? '';

        if ($current->key) {
            $keyName = $current->key->getTranslation('name', 'en') ?? '';
            if ($keyName) {
                $path[] = $keyName;
            }
        }

        return implode(' - ', array_filter($path));
    }

    public function getVariantPathArAttribute()
    {
        if (!$this->variantConfiguration) {
            return '';
        }

        $path = [];
        $current = $this->variantConfiguration;

        $path[] = $current->getTranslation('color', 'ar') ?? '';

        if ($current->key) {
            $keyName = $current->key->getTranslation('name', 'ar') ?? '';
            if ($keyName) {
                $path[] = $keyName;
            }
        }

        return implode(' - ', array_filter($path));
    }

    public function getDiscountAttribute()
    {
        if (($this->has_discount && $this->price_before_discount && $this->price) && $this->price_before_discount != 0) {
            $discount = (($this->price_before_discount - $this->price) / $this->price_before_discount) * 100;
            return round($discount, 2);
        }
        return null;
    }

    public function getQuantityInCartAttribute()
    {
        if (!Auth::check()) {
            return 0;
        }

        // $cartItem = Cart::where('customer_id', Auth::id())
        //     ->where('vendor_product_id', $this->vendorProduct->id)
        //     ->where('vendor_product_variant_id', $this->id)
        //     ->where('type', 'product')
        //     ->first();

        // return $cartItem ? $cartItem->quantity : 0;
        return 0;
    }

    public function getCartIdAttribute()
    {
        return null;
        // return Auth::check()
        //     ? Cart::where('customer_id', Auth::id())
        //         ->where('vendor_product_id', $this->vendorProduct->id)
        //         ->where('vendor_product_variant_id', $this->id)
        //         ->where('type', 'product')
        //         ->first()->id
        //         : null;
    }

    public function getCountDeliveredProductAttribute()
    {
        return (int) $this->fulfillments()
            ->where('status', 'delivered')
            ->sum('allocated_quantity');
    }

    public function getCountOfAvailableAttribute()
    {
        $totalStock = $this->total_stock ?? 0;

        return $totalStock - $this->CountDeliveredProduct;
    }
}
