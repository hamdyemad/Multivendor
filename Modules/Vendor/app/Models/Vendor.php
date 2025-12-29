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

    protected $appends = ['reviews_count', 'average_rating', 'total_balance', 'total_sent', 'total_remaining'];



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
     * Get total balance for this vendor (only delivered orders - vendor's product totals)
     */
    public function getTotalBalanceAttribute()
    {
        // Get the deliver stage ID
        $deliverStage = \Modules\Order\app\Models\OrderStage::withoutGlobalScopes()
            ->where('type', 'deliver')
            ->first();
        
        if (!$deliverStage) {
            return 0;
        }
        
        // Get vendor's order products from delivered orders
        // Sum the product price * quantity for this vendor's products only
        return OrderProduct::where('vendor_id', $this->id)
            ->whereHas('order', function($query) use ($deliverStage) {
                $query->where('stage_id', $deliverStage->id);
            })
            ->sum(DB::raw('price * quantity'));
    }

    public function getBnaiaCommissionAttribute()
    {
        // Get the deliver stage ID
        $deliverStage = \Modules\Order\app\Models\OrderStage::withoutGlobalScopes()
            ->where('type', 'deliver')
            ->first();
        
        if (!$deliverStage) {
            return 0;
        }
        
        // Get delivered order products for this vendor
        $orderProducts = OrderProduct::where('vendor_id', $this->id)
            ->whereHas('order', function($query) use ($deliverStage) {
                $query->where('stage_id', $deliverStage->id);
            })
            ->with('vendorProduct.product.department')
            ->get();
        
        // Calculate commission using department commission
        $totalCommission = 0;
        foreach ($orderProducts as $orderProduct) {
            $productTotal = $orderProduct->price * $orderProduct->quantity;
            $departmentCommission = $orderProduct->vendorProduct?->product?->department?->commission ?? 0;
            $totalCommission += $productTotal * ($departmentCommission / 100);
        }
        
        return $totalCommission;
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
