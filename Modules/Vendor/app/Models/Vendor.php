<?php

namespace Modules\Vendor\app\Models;

use App\Models\BaseModel;
use App\Traits\Translation;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\AutoStoreCountryId;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Attachment;
use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Builder;
use Modules\AreaSettings\app\Models\Country;
use app\Models\Language;
use Illuminate\Support\Facades\DB;
use Modules\CatalogManagement\app\Models\Review;
use Modules\Order\app\Models\OrderProduct;
use Modules\Withdraw\app\Models\Withdraw;

class Vendor extends BaseModel
{
    use HasFactory, SoftDeletes, Translation, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    protected $guarded = [];

    protected $appends = ['reviews_count', 'average_rating', 'total_balance', 'total_sent', 'total_remaining', 'bnaia_commission', 'orders_price'];



    public function total_orders(){
        return $this->hasMany(OrderProduct::class, "vendor_id");
    }

    public function withdraw(){
        return $this->hasMany(Withdraw::class, "reciever_id") ;
    }

    /**
     * Get the user that owns the vendor
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the country
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function logo()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('type', 'logo');
    }

    public function banner()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('type', 'banner');
    }

    public function documents()
    {
        return $this->morphMany(Attachment::class, 'attachable')->where('type', 'docs');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Get the FCM tokens for the vendor
     */
    public function fcmTokens()
    {
        return $this->hasMany(VendorFcmToken::class);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('active', true);
    }


    public function vendorProducts()
    {
        return $this->hasMany(\Modules\CatalogManagement\app\Models\VendorProduct::class);
    }

    /**
     * Get bank products through vendor_products
     */
    public function bankProducts()
    {
        return $this->belongsToMany(\Modules\CatalogManagement\app\Models\Product::class, 'vendor_products')
                    ->withPivot('status', 'rejection_reason')
                    ->withTimestamps();
    }

    /**
     * Get vendor-specific product variants with pricing and stock
     */
    public function vendorProductVariants()
    {
        return $this->hasMany(\Modules\CatalogManagement\app\Models\VendorProductVariant::class);
    }

    /**
     * Get vendor's selected regions for stock management
     */
    public function regions()
    {
        return $this->belongsToMany(\Modules\AreaSettings\app\Models\Region::class, 'vendor_regions')
                    ->withTimestamps();
    }



    /**
     * Get meta keywords as array for specific language
     */
    public function getMetaKeywordsArray($languageCode = 'en')
    {
        $keywordsJson = $this->getTranslation('meta_keywords', $languageCode);

        if (empty($keywordsJson)) {
            return [];
        }

        // Try to decode JSON, fallback to comma-separated string for backward compatibility
        $keywords = json_decode($keywordsJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Fallback: treat as comma-separated string
            return array_map('trim', explode(',', $keywordsJson));
        }

        return is_array($keywords) ? $keywords : [];
    }

    /**
     * Get meta keywords as comma-separated string for specific language
     */
    public function getMetaKeywordsString($languageCode = 'en')
    {
        $keywords = $this->getMetaKeywordsArray($languageCode);
        return implode(', ', $keywords);
    }

    public function getNameAttribute() {
        return $this->getTranslation('name', app()->getLocale());
    }

    public function getReviewsCountAttribute()
    {
        return intval($this->reviews()->count());
    }

    public function getAverageRatingAttribute()
    {
        return intval($this->reviews()->avg('star') ?? 0);
    }

    /**
     * Get total balance for this vendor (orders price - commission)
     */
    public function getTotalBalanceAttribute()
    {
        return $this->orders_price - $this->bnaia_commission;
    }

    public function getBnaiaCommissionAttribute()
    {
        // Get the deliver stage ID
        $deliverStageId = \Modules\Order\app\Models\OrderStage::withoutGlobalScopes()
            ->where('type', 'deliver')
            ->value('id');
        
        if (!$deliverStageId) {
            return 0;
        }

        // Use vendor_order_stages to check if THIS vendor's stage is deliver
        // Get each order product row to calculate commission individually
        $orderProducts = \Illuminate\Support\Facades\DB::table('order_products as op')
            ->join('orders as o', 'op.order_id', '=', 'o.id')
            ->join('vendor_order_stages as vos', function ($join) {
                $join->on('vos.order_id', '=', 'o.id')
                     ->on('vos.vendor_id', '=', 'op.vendor_id');
            })
            ->leftJoin('vendor_products as vp', 'op.vendor_product_id', '=', 'vp.id')
            ->leftJoin('products as p', 'vp.product_id', '=', 'p.id')
            ->leftJoin('departments as d', 'p.department_id', '=', 'd.id')
            ->where('op.vendor_id', $this->id)
            ->where('vos.stage_id', $deliverStageId)
            ->select(
                'op.price',
                'op.shipping_cost',
                'op.commission as product_commission',
                'd.commission as department_commission'
            )
            ->get();

        $totalCommission = 0;
        foreach ($orderProducts as $product) {
            $productPrice = $product->price ?? 0;
            $productShipping = $product->shipping_cost ?? 0;
            
            // Use product commission if set, otherwise use department commission
            $commissionPercent = $product->product_commission > 0 
                ? $product->product_commission 
                : ($product->department_commission ?? 0);
            
            // Calculate commission for this product row (price + shipping)
            $totalWithShipping = $productPrice + $productShipping;
            $totalCommission += ($totalWithShipping * $commissionPercent) / 100;
        }

        return $totalCommission;
    }

    /**
     * Get orders price (total transactions) for this vendor
     * Only includes orders where vendor's stage is deliver (from vendor_order_stages)
     */
    public function getOrdersPriceAttribute()
    {
        // Get the deliver stage ID
        $deliverStageId = \Modules\Order\app\Models\OrderStage::withoutGlobalScopes()
            ->where('type', 'deliver')
            ->value('id');
        
        if (!$deliverStageId) {
            return 0;
        }

        // Use vendor_order_stages to check if THIS vendor's stage is deliver
        $result = \Illuminate\Support\Facades\DB::table('order_products as op')
            ->join('orders as o', 'op.order_id', '=', 'o.id')
            ->join('vendor_order_stages as vos', function ($join) {
                $join->on('vos.order_id', '=', 'o.id')
                     ->on('vos.vendor_id', '=', 'op.vendor_id');
            })
            ->where('op.vendor_id', $this->id)
            ->where('vos.stage_id', $deliverStageId)
            ->select(
                \Illuminate\Support\Facades\DB::raw('SUM(op.price) as products_total'),
                \Illuminate\Support\Facades\DB::raw('SUM(COALESCE(op.shipping_cost, 0)) as shipping_total')
            )
            ->first();

        if (!$result) {
            return 0;
        }

        $productsTotal = $result->products_total ?? 0;
        $shippingTotal = $result->shipping_total ?? 0;

        // Get vendor's fees and discounts from delivered orders
        $extrasResult = \Illuminate\Support\Facades\DB::table('order_extra_fees_discounts as oefd')
            ->join('orders as o', 'oefd.order_id', '=', 'o.id')
            ->join('vendor_order_stages as vos', function ($join) {
                $join->on('vos.order_id', '=', 'o.id')
                     ->on('vos.vendor_id', '=', 'oefd.vendor_id');
            })
            ->where('oefd.vendor_id', $this->id)
            ->where('vos.stage_id', $deliverStageId)
            ->select(
                \Illuminate\Support\Facades\DB::raw('SUM(CASE WHEN oefd.type = "fee" THEN oefd.cost ELSE 0 END) as fees_total'),
                \Illuminate\Support\Facades\DB::raw('SUM(CASE WHEN oefd.type = "discount" THEN oefd.cost ELSE 0 END) as discounts_total')
            )
            ->first();

        $feesTotal = $extrasResult->fees_total ?? 0;
        $discountsTotal = $extrasResult->discounts_total ?? 0;

        // Get vendor's promo code and points shares from delivered orders
        $sharesResult = \Illuminate\Support\Facades\DB::table('vendor_order_stages as vos')
            ->join('orders as o', 'vos.order_id', '=', 'o.id')
            ->where('vos.vendor_id', $this->id)
            ->where('vos.stage_id', $deliverStageId)
            ->select(
                \Illuminate\Support\Facades\DB::raw('SUM(COALESCE(vos.promo_code_share, 0)) as promo_code_total'),
                \Illuminate\Support\Facades\DB::raw('SUM(COALESCE(vos.points_share, 0)) as points_total')
            )
            ->first();

        $promoCodeTotal = $sharesResult->promo_code_total ?? 0;
        $pointsTotal = $sharesResult->points_total ?? 0;

        // Total = products + shipping + fees - discounts - promo_code - points
        return $productsTotal + $shippingTotal + $feesTotal - $discountsTotal - $promoCodeTotal - $pointsTotal;
    }

    /**
     * Get total sent for this vendor
     */
    public function getTotalSentAttribute()
    {
        return $this->withdraw()
            ->where('status', 'accepted')
            ->sum('sent_amount') ?? 0;
    }

    /**
     * Get total remaining for this vendor
     */
    public function getTotalRemainingAttribute()
    {
        return $this->total_balance - $this->total_sent;
    }

    /**
     * Get statistics for a single vendor
     */
    public function getStatistics()
    {
        // Get total balance from orders where this vendor has order products
        $totalBalance = \Modules\Order\app\Models\Order::whereHas('products', function($query) {
            $query->where('vendor_id', $this->id);
        })->sum('total_price') ?? 0;

        // Get total sent from accepted withdrawals for this vendor
        $totalSent = $this->withdraw()
            ->where('status', 'accepted')
            ->sum('sent_amount') ?? 0;

        $totalRemaining = $totalBalance - $totalSent;

        return [
            'total_balance' => $totalBalance,
            'total_sent' => number_format($totalSent, 2),
            'total_remaining' => number_format($totalRemaining, 2),
        ];
    }

    /**
     * Static method to get all vendors statistics
     */
    public static function getVendorsStatistics()
    {
        $vendors = static::all();
        $totalBalance = 0;
        $totalSent = 0;
        $totalRemaining = 0;

        foreach ($vendors as $vendor) {
            // Add to totals
            $totalBalance += $vendor->total_balance;
            $totalSent += $vendor->total_sent;
            $totalRemaining += $vendor->total_remaining;
        }

        return [
            'total_balance' => number_format($totalBalance, 2),
            'total_sent' => number_format($totalSent, 2),
            'total_remaining' => number_format($totalRemaining, 2),
        ];
    }

    // Scopes

    /**
     * Apply custom search logic for Vendor
     * Searches in vendor name translations and user email
     */
    protected function applyCustomSearch(Builder $query, string $search): Builder
    {
        return $query->orWhereHas('user', function ($q) use ($search) {
            $q->where('email', 'like', "%{$search}%");
        });
    }

    /**
     * Get the vendor's departments
     */
    public function departments()
    {
        return $this->belongsToMany(\Modules\CategoryManagment\app\Models\Department::class, 'department_vendor')
                    ->withTimestamps();
    }

    public function scopeByDepartment(Builder $query, $departmentIdentifier)
    {
        return $query->whereHas('departments', function ($q) use ($departmentIdentifier) {
            $q->where('departments.id', $departmentIdentifier)
              ->orWhere('departments.slug', $departmentIdentifier);
        });
    }



    /**
     * Override filter scope to add vendor-specific filters
     * Calls parent filter from HasFilterScopes trait and adds custom filters
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        // Call parent filter scope from HasFilterScopes trait
        parent::scopeFilter($query, $filters);

        // Filter by ID
        if (isset($filters['id']) && $filters['id'] !== '') {
            $query->where('id', $filters['id']);
        }

        // Filter by country
        if (!empty($filters['country_id'])) {
            $query->byCountry($filters['country_id']);
        }

        // Filter by department
        if (!empty($filters['department_id'])) {
            $query->byDepartment($filters['department_id']);
        }

        if (!empty($filters['created_at'])) {
            $query->where('created_at', $filters['created_at']);
        }

        return $query;
    }


}
