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

    /**
     * Get the vendor's refund settings
     */
    public function refundSettings()
    {
        return $this->hasOne(\Modules\Refund\app\Models\VendorRefundSetting::class);
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

        // Calculate commission from delivered orders
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
                'op.id',
                'op.quantity',
                'op.price',
                'op.shipping_cost',
                'op.commission as product_commission',
                'd.commission as department_commission'
            )
            ->get();

        $totalCommission = 0;
        foreach ($orderProducts as $product) {
            // Use product commission directly (don't fallback to department if 0)
            $commissionPercent = $product->product_commission ?? 0;
            
            // Calculate commission on product price + shipping
            $totalWithShipping = ($product->price ?? 0) + ($product->shipping_cost ?? 0);
            $totalCommission += ($totalWithShipping * $commissionPercent) / 100;
        }

        // Add commission on fees and discounts
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
        $netExtras = $feesTotal - $discountsTotal;

        // Calculate average commission rate from products
        $totalProductsAmount = $orderProducts->sum(fn($p) => ($p->price ?? 0) + ($p->shipping_cost ?? 0));

        // Calculate commission on net extras using average rate
        if ($totalProductsAmount > 0 && $netExtras > 0) {
            $avgCommissionRate = ($totalCommission / $totalProductsAmount) * 100;
            $extrasCommission = ($netExtras * $avgCommissionRate) / 100;
            $totalCommission += $extrasCommission;
        }

        // Calculate commission returned from refunds
        // Read directly from refund_request_items and order_products (not from accounting_entries)
        $refundedCommission = \Illuminate\Support\Facades\DB::table('refund_request_items as rri')
            ->join('refund_requests as rr', 'rri.refund_request_id', '=', 'rr.id')
            ->join('order_products as op', 'rri.order_product_id', '=', 'op.id')
            ->where('rr.vendor_id', $this->id)
            ->where('rr.status', 'refunded')
            ->sum(\Illuminate\Support\Facades\DB::raw('(rri.total_price + rri.shipping_amount) * COALESCE(op.commission, 0) / 100')) ?? 0;

        return $totalCommission - $refundedCommission;
    }

    /**
     * Get orders price (total transactions) for this vendor
     * Only includes orders where vendor's stage is deliver (from vendor_order_stages)
     * Subtracts completed refunds
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

        // Calculate total from order products for delivered orders
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
                \Illuminate\Support\Facades\DB::raw('SUM(op.shipping_cost) as shipping_total')
            )
            ->first();

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

        // Calculate total orders price before refunds
        // Vendor earnings = Products + Shipping + Fees - Discounts - Commission
        // Do NOT subtract promo_code_share and points_share here, as they are used to calculate commission
        // The commission already accounts for platform's share of discounts
        $totalBeforeRefunds = $productsTotal + $shippingTotal + $feesTotal - $discountsTotal;

        // Subtract completed refunds (total_refund_amount already includes all calculations)
        $totalRefunded = \Illuminate\Support\Facades\DB::table('refund_requests')
            ->where('vendor_id', $this->id)
            ->where('status', 'refunded')
            ->sum('total_refund_amount') ?? 0;

        return $totalBeforeRefunds - $totalRefunded;
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
     * Static method to get all vendors statistics using optimized database queries
     * Subtracts completed refunds from totals
     */
    public static function getVendorsStatistics($countryId = null)
    {
        // Get the deliver stage ID once
        $deliverStageId = \Modules\Order\app\Models\OrderStage::withoutGlobalScopes()
            ->where('type', 'deliver')
            ->value('id');
        
        if (!$deliverStageId) {
            return [
                'total_balance' => number_format(0, 2),
                'total_commission' => number_format(0, 2),
                'total_sent' => number_format(0, 2),
                'total_remaining' => number_format(0, 2),
                'total_transactions' => number_format(0, 2),
            ];
        }

        // Calculate total from order products for delivered orders
        $ordersQuery = \Illuminate\Support\Facades\DB::table('order_products as op')
            ->join('orders as o', 'op.order_id', '=', 'o.id')
            ->join('vendor_order_stages as vos', function ($join) {
                $join->on('vos.order_id', '=', 'o.id')
                     ->on('vos.vendor_id', '=', 'op.vendor_id');
            })
            ->where('vos.stage_id', $deliverStageId);
        
        if ($countryId) {
            $ordersQuery->where('o.country_id', $countryId);
        }
        
        $ordersResult = $ordersQuery->selectRaw('
            SUM(op.price) as products_total,
            SUM(op.shipping_cost) as shipping_total
        ')->first();

        $productsTotal = $ordersResult->products_total ?? 0;
        $shippingTotal = $ordersResult->shipping_total ?? 0;

        // Get fees and discounts
        $extrasQuery = \Illuminate\Support\Facades\DB::table('order_extra_fees_discounts as oefd')
            ->join('orders as o', 'oefd.order_id', '=', 'o.id')
            ->join('vendor_order_stages as vos', function ($join) {
                $join->on('vos.order_id', '=', 'o.id')
                     ->on('vos.vendor_id', '=', 'oefd.vendor_id');
            })
            ->where('vos.stage_id', $deliverStageId);
        
        if ($countryId) {
            $extrasQuery->where('o.country_id', $countryId);
        }
        
        $extrasResult = $extrasQuery->selectRaw('
            SUM(CASE WHEN oefd.type = "fee" THEN oefd.cost ELSE 0 END) as fees_total,
            SUM(CASE WHEN oefd.type = "discount" THEN oefd.cost ELSE 0 END) as discounts_total
        ')->first();

        $feesTotal = $extrasResult->fees_total ?? 0;
        $discountsTotal = $extrasResult->discounts_total ?? 0;

        // Get promo code and points shares
        $sharesQuery = \Illuminate\Support\Facades\DB::table('vendor_order_stages as vos')
            ->join('orders as o', 'vos.order_id', '=', 'o.id')
            ->where('vos.stage_id', $deliverStageId);
        
        if ($countryId) {
            $sharesQuery->where('o.country_id', $countryId);
        }
        
        $sharesResult = $sharesQuery->selectRaw('
            SUM(COALESCE(vos.promo_code_share, 0)) as promo_code_total,
            SUM(COALESCE(vos.points_share, 0)) as points_total
        ')->first();

        $promoCodeTotal = $sharesResult->promo_code_total ?? 0;
        $pointsTotal = $sharesResult->points_total ?? 0;

        // Calculate total before refunds
        // Vendor earnings = Products + Shipping + Fees - Discounts - Commission
        // Do NOT subtract promo_code_share and points_share here, as they are used to calculate commission
        // The commission already accounts for platform's share of discounts
        $totalBeforeRefunds = $productsTotal + $shippingTotal + $feesTotal - $discountsTotal;

        // Subtract completed refunds
        $refundsQuery = \Illuminate\Support\Facades\DB::table('refund_requests')
            ->where('status', 'refunded');
        
        if ($countryId) {
            $refundsQuery->where('country_id', $countryId);
        }
        
        $totalRefunded = $refundsQuery->sum('total_refund_amount') ?? 0;

        // Total transactions after refunds
        $totalTransactions = $totalBeforeRefunds - $totalRefunded;

        // Calculate total commission from delivered orders
        $commissionQuery = \Illuminate\Support\Facades\DB::table('order_products as op')
            ->join('orders as o', 'op.order_id', '=', 'o.id')
            ->join('vendor_order_stages as vos', function ($join) {
                $join->on('vos.order_id', '=', 'o.id')
                     ->on('vos.vendor_id', '=', 'op.vendor_id');
            })
            ->leftJoin('vendor_products as vp', 'op.vendor_product_id', '=', 'vp.id')
            ->leftJoin('products as p', 'vp.product_id', '=', 'p.id')
            ->leftJoin('departments as d', 'p.department_id', '=', 'd.id')
            ->where('vos.stage_id', $deliverStageId);
        
        if ($countryId) {
            $commissionQuery->where('o.country_id', $countryId);
        }
        
        $commissionResult = $commissionQuery->selectRaw('
            SUM(
                (op.price + op.shipping_cost) * 
                COALESCE(op.commission, 0) / 100
            ) as total_commission
        ')->first();

        $totalCommissionBeforeRefunds = $commissionResult->total_commission ?? 0;

        // Calculate commission returned from refunds
        // Read directly from refund_request_items and order_products (not from accounting_entries)
        $refundedCommissionQuery = \Illuminate\Support\Facades\DB::table('refund_request_items as rri')
            ->join('refund_requests as rr', 'rri.refund_request_id', '=', 'rr.id')
            ->join('order_products as op', 'rri.order_product_id', '=', 'op.id')
            ->where('rr.status', 'refunded');
        
        if ($countryId) {
            $refundedCommissionQuery->where('rr.country_id', $countryId);
        }
        
        $refundedCommission = $refundedCommissionQuery->sum(
            \Illuminate\Support\Facades\DB::raw('(rri.total_price + rri.shipping_amount) * COALESCE(op.commission, 0) / 100')
        ) ?? 0;

        // Total commission after refunds
        $totalCommission = $totalCommissionBeforeRefunds - $refundedCommission;

        // Total balance = transactions - commission
        $totalBalance = $totalTransactions - $totalCommission;

        // Get total sent (accepted withdrawals)
        $withdrawQuery = \Illuminate\Support\Facades\DB::table('withdraws')
            ->where('status', 'accepted');
        
        if ($countryId) {
            $withdrawQuery->where('country_id', $countryId);
        }
        
        $totalSent = $withdrawQuery->sum('sent_amount') ?? 0;

        // Total remaining = balance - sent
        $totalRemaining = $totalBalance - $totalSent;

        return [
            'total_balance' => number_format($totalBalance, 2),
            'total_commission' => number_format($totalCommission, 2),
            'total_sent' => number_format($totalSent, 2),
            'total_remaining' => number_format($totalRemaining, 2),
            'total_transactions' => number_format($totalTransactions, 2),
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
