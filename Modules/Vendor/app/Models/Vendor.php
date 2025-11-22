<?php

namespace Modules\Vendor\app\Models;

use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Attachment;
use App\Models\Traits\HasSlug;
use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Builder;
use Modules\AreaSettings\app\Models\Country;
use Modules\CategoryManagment\app\Models\Activity;
use Modules\Order\app\Models\OrderProduct;
use Modules\Withdraw\app\Models\Withdraw;

class Vendor extends Model
{
    use HasFactory, SoftDeletes, Translation, HumanDates, HasSlug;

    protected $guarded = [];

    // HasSlug trait configuration
    protected $slugWithRandomSuffix = true;
    protected $slugSuffixLength = 6;


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

    /**
     * Get the activity (single - for backward compatibility)
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get the vendor's activities (many-to-many)
     */
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'vendors_activities', 'vendor_id', 'activity_id');
    }

    /**
     * Alias for attachments relationship
     */
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

    /**
     * Get the vendor's commission
     */
    public function commission()
    {
        return $this->hasOne(VendorCommission::class);
    }

    /**
     * Get vendor products (bank products added by this vendor)
     */
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


    // Getters

    public function getNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale()) ?? '--';
    }

    public function scopeFilter(Builder $query, $filters)
    {

        // Filter by active status
        if (isset($filters['id']) && $filters['id'] !== '') {
            $query->where('id', $filters['id']);
        }

        // Search in translations or user email
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('translations', function ($query) use ($searchTerm) {
                    $query->where('lang_key', 'name')
                        ->where('lang_value', 'like', '%' . $searchTerm . '%');
                })
                    ->orWhereHas('user', function ($query) use ($searchTerm) {
                        $query->where('email', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        // Filter by active status
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        // Filter by country
        if (!empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        // Filter by date range
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }
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

    /**
     * Override slug source text method to handle vendor translations properly
     */
    protected function getSlugSourceText()
    {
        // Try to get from existing translations first
        $name = $this->getTranslation('name', 'en');

        if (!empty($name)) {
            return $name;
        }

        // Fallback to any available translation
        $name = $this->getTranslation('name', 'ar');

        if (!empty($name)) {
            return $name;
        }

        // Final fallback
        return $this->getSlugFallbackText();
    }

    /**
     * Override slug fallback text
     */
    protected function getSlugFallbackText()
    {
        return 'vendor-' . ($this->id ?: time());
    }

    /**
     * Public method to generate slug (calls protected trait method)
     */
    public function createSlug()
    {
        return $this->generateSlug();
    }

}
