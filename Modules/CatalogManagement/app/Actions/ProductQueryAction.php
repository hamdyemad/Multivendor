<?php

namespace Modules\CatalogManagement\app\Actions;

use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Modules\CatalogManagement\app\Models\VendorProduct;

class ProductQueryAction
{
    /**
     * Handle the product query with filters and sorting
     */
    public function handle(array $filters = [])
    {
        $query = VendorProduct::query()
            ->active()
            ->status(VendorProduct::STATUS_APPROVED)
            ->with([
                'product.department.translations',
                'product.category.translations',
                'product.subCategory.translations',
                'product.brand.translations',
                'product.translations',
                'product.mainImage',
                'product.additionalImages',
                'variants' => function ($q) use ($filters) {
                    // Filter variants by price range if provided
                    if (!empty($filters['min_price'])) {
                        $q->where('price', '>=', $filters['min_price']);
                    }
                    if (!empty($filters['max_price'])) {
                        $q->where('price', '<=', $filters['max_price']);
                    }
                    $q->with([
                        'variantConfiguration.translations',
                        'variantConfiguration.key.translations',
                        'variantConfiguration.parent_data.translations',
                        'variantConfiguration.parent_data.key.translations',
                    ])
                    ->withSum('stocks as total_stock_sum', 'quantity')
                    ->withSum(['stockBookings as booked_stock_sum' => fn($q) => $q->where('status', 'booked')], 'booked_quantity')
                    ->withSum(['stockBookings as allocated_stock_sum' => fn($q) => $q->where('status', 'allocated')], 'booked_quantity')
                    ->withSum(['stockBookings as fulfilled_stock_sum' => fn($q) => $q->where('status', 'fulfilled')], 'booked_quantity')
                    ->withSum(['fulfillments as delivered_stock_sum' => fn($q) => $q->where('status', 'delivered')], 'allocated_quantity');
                },
                'vendor',
                'taxes',
            ])
            ->withSum(['stockBookings as booked_stock_sum' => fn($q) => $q->where('status', 'booked')], 'booked_quantity')
            ->withSum(['stockBookings as allocated_stock_sum' => fn($q) => $q->where('status', 'allocated')], 'booked_quantity')
            ->withSum(['stockBookings as fulfilled_stock_sum' => fn($q) => $q->where('status', 'fulfilled')], 'booked_quantity')
            ->withCount(['reviews' => function($q) {
                $q->withoutGlobalScope('country_filter');
            }])
            ->withAvg(['reviews' => function($q) {
                $q->withoutGlobalScope('country_filter');
            }], 'star')
            ->withExists(['wishlist as is_fav' => function($q) {
                // Use default and sanctum guards
                $user = auth()->user() ?? auth()->guard('sanctum')->user();
                if (!$user) {
                    $q->whereRaw('1=0');
                } else {
                    $q->where('customer_id', $user->id);
                }
            }]);

        if (!empty($filters)) {
            $query->filter($filters);
        }

        $query = $this->applySorting($query, $filters);

        return $query;
    }

    protected function applySorting($query, array $filters)
    {
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortType = $filters['sort_type'] ?? 'asc';

        if (!in_array($sortType, ['asc', 'desc'])) {
            $sortType = 'desc';
        }

        switch ($sortBy) {
            case 'name':
                // Sort by product name using join to avoid country_id ambiguity
                // Get language ID from Language model based on current locale
                $currentLocale = app()->getLocale();
                $langId = Language::where('code', $currentLocale)->value('id');
                
                // If no language found, try to get by common codes
                if (!$langId) {
                    $langId = $currentLocale === 'ar' ? 1 : 2;
                }
                
                // Validate sort type
                $sortDirection = strtolower($sortType) === 'asc' ? 'ASC' : 'DESC';
                
                // Join products and translations tables with LEFT JOIN to not filter out products
                // Use a subquery approach to get the translation value
                $query->leftJoin('products', 'vendor_products.product_id', '=', 'products.id')
                    ->leftJoin('translations', function($join) use ($langId) {
                        $join->on('products.id', '=', 'translations.translatable_id')
                             ->where('translations.translatable_type', '=', 'Modules\\CatalogManagement\\app\\Models\\Product')
                             ->where('translations.lang_key', '=', 'name')
                             ->where('translations.lang_id', '=', $langId);
                    })
                    ->select('vendor_products.*')
                    ->groupBy('vendor_products.id')
                    ->orderByRaw("COALESCE(MAX(translations.lang_value), vendor_products.id) COLLATE utf8mb4_unicode_ci {$sortDirection}");
                break;
                
            case 'price':
                // Sort by minimum variant price using relationship
                $query->leftJoin('vendor_product_variants', 'vendor_products.id', '=', 'vendor_product_variants.vendor_product_id')
                    ->select('vendor_products.*')
                    ->selectRaw('MIN(vendor_product_variants.price) as min_price')
                    ->groupBy('vendor_products.id')
                    ->orderBy('min_price', $sortType);
                break;
                
            case 'rating':
                // Sort by average rating
                $query->orderBy('reviews_avg_star', $sortType);
                break;
                
            case 'views':
                // Sort by views
                $query->orderBy('vendor_products.views', $sortType);
                break;
                
            case 'sales':
                // Sort by number of sales
                $query->orderBy('vendor_products.sales', $sortType);
                break;
                
            default:
                // Default sort by created_at
                $query->orderBy('vendor_products.sort_number', $sortType);
        }

        return $query;
    }
}
