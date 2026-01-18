<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HumanDates;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\AutoStoreCountryId;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Vendor\app\Models\Vendor;
use Illuminate\Database\Eloquent\Builder;
use Modules\Order\app\Models\Wishlist;
use Modules\CatalogManagement\app\Models\StockBooking;

class VendorProduct extends BaseModel
{
    use HasFactory, SoftDeletes, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $guarded = [];
    protected $casts = [
        'points' => 'integer',
        'max_per_order' => 'integer',
        'offer_date_view' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_able_to_refund' => 'boolean',
        'refund_days' => 'integer',
    ];

    // Removed appends to avoid N+1 queries - use withCount and withAvg instead
    // protected $appends = ['reviews_count', 'average_rating', 'is_fav'];

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
     * Get the vendor that owns this vendor product
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the taxes for this vendor product (many-to-many)
     */
    public function taxes()
    {
        return $this->belongsToMany(Tax::class, 'vendor_product_taxes', 'vendor_product_id', 'tax_id')
            ->withTimestamps();
    }

    /**
     * Get vendor product variants
     */
    public function variants()
    {
        return $this->hasMany(VendorProductVariant::class);
    }

    /**
     * Get reviews for this vendor product
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get order products for this vendor product
     */
    public function orderProducts()
    {
        return $this->hasMany(\Modules\Order\app\Models\OrderProduct::class);
    }

    public function highestDiscountVariant()
    {
        return $this->hasOne(VendorProductVariant::class)
            ->orderBy('price', 'desc');
    }

    /**
     * Apply custom search logic for VendorProduct
     * Searches through product relationships (brand, category, product name)
     */
    protected function applyCustomSearch(Builder $query, string $search): Builder
    {
        return $query->orWhereHas('product', function($subQ) use ($search) {
                $subQ->whereHas('translations', function($subSubQ) use ($search) {
                    $subSubQ->where('lang_value', 'like', "%{$search}%");
                });
            })
            ->orWhereHas('product.brand', function($subQ) use ($search) {
                $subQ->whereHas('translations', function($subSubQ) use ($search) {
                    $subSubQ->where('lang_value', 'like', "%{$search}%");
                });
            })
            ->orWhereHas('product.category', function($subQ) use ($search) {
                $subQ->whereHas('translations', function($subSubQ) use ($search) {
                    $subSubQ->where('lang_value', 'like', "%{$search}%");
                });
            });
    }

    /**
     * Scope: Filter by price range (through variants)
     * Filters products where the minimum variant price falls within the range
     */
    public function scopePriceRange(Builder $query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice !== null || $maxPrice !== null) {
            $query->where(function($q) use ($minPrice, $maxPrice) {
                if ($minPrice !== null && $maxPrice !== null) {
                    // Both min and max provided - check if product has any variant in range
                    $q->whereRaw(
                        'EXISTS (SELECT 1 FROM vendor_product_variants WHERE vendor_product_id = vendor_products.id AND price BETWEEN ? AND ?)',
                        [$minPrice, $maxPrice]
                    );
                } elseif ($minPrice !== null) {
                    // Only min provided - check if product has any variant >= min
                    $q->whereRaw(
                        'EXISTS (SELECT 1 FROM vendor_product_variants WHERE vendor_product_id = vendor_products.id AND price >= ?)',
                        [$minPrice]
                    );
                } elseif ($maxPrice !== null) {
                    // Only max provided - check if product has any variant <= max
                    $q->whereRaw(
                        'EXISTS (SELECT 1 FROM vendor_product_variants WHERE vendor_product_id = vendor_products.id AND price <= ?)',
                        [$maxPrice]
                    );
                }
            });
        }
        return $query;
    }

    public function scopeByDepartment(Builder $query, $departmentIdentifier)
    {
        return $query->whereHas('product', function($subQ) use ($departmentIdentifier) {
            $subQ->whereHas('department', function($subSubQ) use ($departmentIdentifier) {
                $subSubQ->where('id', $departmentIdentifier)->orWhere('slug', $departmentIdentifier);
            });
        });
    }

    public function scopeByCategory(Builder $query, $categoryIdentifier)
    {
        return $query->whereHas('product', function($subQ) use ($categoryIdentifier) {
            $subQ->whereHas('category', function($subSubQ) use ($categoryIdentifier) {
                $subSubQ->where('id', $categoryIdentifier)->orWhere('slug', $categoryIdentifier);
            });
        });
    }

    public function scopeBySubCategory(Builder $query, $subCategoryIdentifier)
    {
        return $query->whereHas('product', function($subQ) use ($subCategoryIdentifier) {
            $subQ->whereHas('subCategory', function($subSubQ) use ($subCategoryIdentifier) {
                $subSubQ->where('id', $subCategoryIdentifier)->orWhere('slug', $subCategoryIdentifier);
            });
        });
    }

    public function scopeByBrand(Builder $query, $brandIdentifier)
    {
        return $query->whereHas('product', function($subQ) use ($brandIdentifier) {
            $subQ->whereHas('brand', function($subSubQ) use ($brandIdentifier) {
                $subSubQ->where('id', $brandIdentifier)->orWhere('slug', $brandIdentifier);
            });
        });
    }

    /**
     * Scope: Filter by country (through product relationship)
     */
    public function scopeByCountry(Builder $query, $countryIdentifier)
    {
        return $query->whereHas('product', function($subQ) use ($countryIdentifier) {
            $subQ->whereHas('country', function($subSubQ) use ($countryIdentifier) {
                $subSubQ->where('id', $countryIdentifier)->orWhere('slug', $countryIdentifier);
            });
        });
    }

    /**
     * Scope: Filter featured products
     */
    public function scopeFeatured(Builder $query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('vendor_products.is_active', true);
    }

    public function scopeHasDiscount(Builder $query)
    {
        // Check for products with variants that have active discounts
        // OR products without variants but with discount fields on the vendor_product itself
        $query->where(function($q) {
            // Products with variants having active discount
            $q->whereHas('variants', function($subQ) {
                $subQ->where('has_discount', true)
                    ->whereNotNull('discount_end_date')
                    ->where('discount_end_date', '>', now());
            });
        });

        // Eager load only variants with active discounts
        $query->with(['variants' => function($subQ) {
            $subQ->where('has_discount', true)
                ->whereNotNull('discount_end_date')
                ->where('discount_end_date', '>', now());
        }]);

        return $query;
    }

    /**
     * Scope: Filter products WITHOUT active discount (offer=false)
     */
    public function scopeNoDiscount(Builder $query)
    {
        // Products where NO variants have active discounts
        $query->where(function($q) {
            $q->whereDoesntHave('variants', function($subQ) {
                $subQ->where('has_discount', true)
                    ->whereNotNull('discount_end_date')
                    ->where('discount_end_date', '>', now());
            });
        });

        return $query;
    }

    public function scopeStatus(Builder $query, $status)
    {
        return $query->where('status', $status);
    }
    /**
     * Scope: Override filter to add price range and featured filters
     * VendorProduct doesn't have translations, so we skip the default search
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        // Handle search through product relationship instead of direct translations
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('product', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('sku', 'like', "%{$search}%")
                        ->orWhereHas('translations', function ($q) use ($search) {
                            $q->where('lang_key', 'title')
                            ->where('lang_value', 'like', "%{$search}%");
                        })
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orWhereHas('variants', function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%");
            });
            // Remove search from filters to prevent parent from processing it
            unset($filters['search']);
        }

        // Vendor filter
        if (!empty($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        // Brand filter
        if (!empty($filters['brand_id'])) {
            $query->byBrand($filters['brand_id']);
        }

        // Department filter
        if (!empty($filters['department_id'])) {
            $query->byDepartment($filters['department_id']);
        }

        // Category filter
        if (!empty($filters['category_id'])) {
            $query->byCategory($filters['category_id']);
        }

        // Product Type filter (product/bank)
        if (!empty($filters['product_type'])) {
            $query->whereHas('product', function($q) use ($filters) {
                $q->where('type', $filters['product_type']);
            });
        }

        // Configuration Type filter (simple/variants)
        if (!empty($filters['configuration_type'])) {
            $query->whereHas('product', function($q) use ($filters) {
                $q->where('configuration_type', $filters['configuration_type']);
            });
        }

        // Active Status filter (is_active)
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        // Stock Status filter
        if (!empty($filters['stock_status'])) {
            if ($filters['stock_status'] === 'in_stock') {
                $query->whereHas('variants', function($q) {
                    $q->whereHas('stocks', function($sq) {
                        $sq->where('quantity', '>', 0);
                    });
                });
            } elseif ($filters['stock_status'] === 'out_of_stock') {
                $query->whereDoesntHave('variants.stocks', function($sq) {
                    $sq->where('quantity', '>', 0);
                });
            }
        }

        // Approval Status filter (status: pending/approved/rejected)
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Created From date filter
        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        // Created To date filter
        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        // Call parent filter scope from trait (without search)
        parent::scopeFilter($query, $filters);

        // Price range filter
        if (!empty($filters['min_price']) || !empty($filters['max_price'])) {
            $query->priceRange(
                $filters['min_price'] ?? null,
                $filters['max_price'] ?? null
            );
        }

        // Featured filter
        if (!empty($filters['featured'])) {
            $query->featured();
        }

        // Has discount filter (legacy support)
        if (!empty($filters['has_discount'])) {
            $query->hasDiscount();
        }

        // Offer filter - true = products with active offers, false = products without offers
        if (isset($filters['offer'])) {
            if ($filters['offer'] === true || $filters['offer'] === 'true' || $filters['offer'] === 1 || $filters['offer'] === '1') {
                $query->hasDiscount();
            } else {
                $query->noDiscount();
            }
        }

        // Variant filter - filter by variant configuration IDs (e.g., variant=1,2,3)
        // Only returns products that have variants with the specified configuration IDs
        if (!empty($filters['variant'])) {
            $variantIds = is_array($filters['variant']) 
                ? $filters['variant'] 
                : explode(',', $filters['variant']);
            
            // Filter to only numeric IDs
            $variantIds = array_filter(array_map('intval', $variantIds));
            
            if (!empty($variantIds)) {
                $query->whereHas('variants', function ($q) use ($variantIds) {
                    // Only match variants that have a configuration (not simple products)
                    $q->whereNotNull('variant_configuration_id')
                      ->whereIn('variant_configuration_id', $variantIds);
                });
            }
        }

        if (!empty($filters['rate'])) {
            // Filter products where the average rating equals the requested rate
            $query->whereRaw(
                '(SELECT AVG(star) FROM reviews WHERE reviewable_id = vendor_products.id AND reviewable_type = ?) = ?',
                [get_class($this), $filters['rate']]
            );

            $query->with(['reviews' => function($subQ) use ($filters) {
                $subQ->where('star', $filters['rate']);
            }]);
        }

        return $query;
    }

    public function getReviewsCountAttribute()
    {
        return intval($this->reviews()->count());
    }

    public function getAverageRatingAttribute()
    {
        return intval($this->reviews()->avg('star') ?? 0);
    }

    public function getIsFavAttribute()
    {
        // Check both default and sanctum guards for authenticated user
        $user = auth()->user() ?? auth()->guard('sanctum')->user();
        
        // For guests, always return false
        if (!$user) {
            return false;
        }

        // Use direct query with withoutGlobalScopes to avoid country filtering issues
        return \Modules\Order\app\Models\Wishlist::withoutGlobalScopes()
            ->where('customer_id', $user->id)
            ->where('vendor_product_id', $this->id)
            ->exists();
    }

    /**
     * Get total stock across all variants
     */
    public function getTotalStockAttribute()
    {
        if ($this->relationLoaded('variants')) {
            return $this->variants->sum('total_stock') ?? 0;
        }
        return $this->variants()->get()->sum('total_stock') ?? 0;
    }

    /**
     * Get total booked stock across all variants
     */
    public function getBookedStockAttribute()
    {
        return (int) StockBooking::whereIn('vendor_product_variant_id', 
            $this->variants()->pluck('id')
        )->where('status', StockBooking::STATUS_BOOKED)
        ->sum('booked_quantity');
    }

    /**
     * Get total allocated stock across all variants
     */
    public function getAllocatedStockAttribute()
    {
        return (int) StockBooking::whereIn('vendor_product_variant_id', 
            $this->variants()->pluck('id')
        )->where('status', StockBooking::STATUS_ALLOCATED)
        ->sum('booked_quantity');
    }

    /**
     * Get total fulfilled stock across all variants
     */
    public function getFulfilledStockAttribute()
    {
        return (int) StockBooking::whereIn('vendor_product_variant_id', 
            $this->variants()->pluck('id')
        )->where('status', StockBooking::STATUS_FULFILLED)
        ->sum('booked_quantity');
    }

    /**
     * Get remaining stock across all variants (total - booked - allocated - fulfilled)
     */
    public function getRemainingStockAttribute()
    {
        return max(0, $this->total_stock - $this->booked_stock - $this->allocated_stock - $this->fulfilled_stock);
    }

    /**
     * Check if product is eligible for refund based on delivery date
     * 
     * @param \Carbon\Carbon|string|null $deliveredAt The delivery date
     * @return bool
     */
    public function isEligibleForRefund($deliveredAt = null): bool
    {
        // Check if refunds are enabled for this product
        if (!$this->is_able_to_refund) {
            return false;
        }

        // If no delivery date provided, assume it's eligible (will be checked later)
        if (!$deliveredAt) {
            return true;
        }

        // Convert to Carbon instance if string
        $deliveredAt = $deliveredAt instanceof \Carbon\Carbon 
            ? $deliveredAt 
            : \Carbon\Carbon::parse($deliveredAt);

        // Check if within refund window
        $refundDeadline = $deliveredAt->copy()->addDays($this->refund_days);
        
        return now()->lte($refundDeadline);
    }

    /**
     * Get the refund deadline for this product based on delivery date
     * 
     * @param \Carbon\Carbon|string $deliveredAt The delivery date
     * @return \Carbon\Carbon
     */
    public function getRefundDeadline($deliveredAt): \Carbon\Carbon
    {
        $deliveredAt = $deliveredAt instanceof \Carbon\Carbon 
            ? $deliveredAt 
            : \Carbon\Carbon::parse($deliveredAt);

        return $deliveredAt->copy()->addDays($this->refund_days);
    }
}
