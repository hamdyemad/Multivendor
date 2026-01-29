<?php

namespace Modules\CatalogManagement\app\Repositories\Api;

use Illuminate\Support\Facades\DB;
use App\Actions\IsPaginatedAction;
use Modules\CatalogManagement\app\Actions\ProductQueryAction;
use Modules\CatalogManagement\app\Interfaces\Api\ProductApiRepositoryInterface;
use Modules\CatalogManagement\app\Models\Product;
use Modules\CatalogManagement\app\Models\Brand;
use Modules\CatalogManagement\app\Models\VariantConfigurationKey;
use Modules\CatalogManagement\app\Models\VariantsConfiguration;
use Illuminate\Support\Facades\Auth;
use Modules\CatalogManagement\app\DTOs\ProductFilterDTO;
use Modules\CatalogManagement\app\Models\VendorProduct;

class ProductApiRepository implements ProductApiRepositoryInterface
{
    public function __construct(
        private ProductQueryAction $query,
        private IsPaginatedAction $paginated
    ) {}

    /**
     * Get all products with filtering and pagination
     */
    public function getAllProducts(ProductFilterDTO $dto)
    {
        $filters = $dto->toArray();
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $dto->per_page, 'true');
        return $result;
    }

    /**
     * Get specific product by ID or slug
     */
    public function getProductByIdOrSlug(string $identifier, string $vendorId)
    {
        $query = $this->query->handle([]);

        return $query->where(function ($q) use ($identifier) {
                $q->where('id', $identifier)
                ->orWhereHas('product', function ($subQ) use ($identifier) {
                    $subQ->where('slug', $identifier);
                });
            })->where(function ($q) use ($vendorId) {
                $q->byVendor($vendorId);
            })
            ->with([
                // 'approvedReviews',
                'product' => function ($q) {
                    $q->with(['department', 'category', 'subCategory']);
                },
                'variants' => function ($q) {
                    $q->with([
                        'variantConfiguration.parent_data.key',
                        'variantConfiguration.key'
                    ]);
                }
            ])
            ->first();
    }

    /**
     * Increment product views
     */
    public function incrementProductViews(string $productId)
    {
        $query = $this->query->handle([]);

        return $query->where('id', $productId)->increment('views');
    }

    public function incrementProductSales(string $productId, $quantity)
    {
        $query = $this->query->handle([]);

        return $query->where('id', $productId)->increment('sales', $quantity);
    }

    /**
     * Store product review
     */
    public function findProduct(string $id)
    {
        return $this->query->handle([])
        ->where('id', $id)
        ->first();
    }

    /**
     * Find vendor product with all relationships for order creation pipeline
     */
    public function findProductForOrder(string $id)
    {
        // Query directly without ProductQueryAction filters (active/approved)
        // to allow checkout for any valid product
        $vendorProduct = \Modules\CatalogManagement\app\Models\VendorProduct::query()
            ->where('id', $id)
            ->with([
                'product' => function ($q) {
                    $q->with([
                        'brand',
                        'department',
                        'category',
                        'subCategory',
                        'translations'
                    ]);
                },
                'vendor',
                'taxes',
                'variants' => function ($q) {
                    $q->with([
                        'variantConfiguration.parent_data.key',
                        'variantConfiguration.key',
                        'stocks'
                    ]);
                }
            ])
            ->first();

        if (!$vendorProduct) {
            return null;
        }

        // Extract product translations using getTranslation method
        $productNameEn = $vendorProduct->product->getTranslation('title', 'en') ?? $vendorProduct->product->title;
        $productNameAr = $vendorProduct->product->getTranslation('title', 'ar') ?? $vendorProduct->product->title;

        // Calculate total tax rate from all taxes
        $taxes = $vendorProduct->taxes ?? collect();
        $taxRate = $taxes->sum('percentage');
        $taxNames = ['en' => [], 'ar' => []];
        foreach ($taxes as $tax) {
            $taxNames['en'][] = $tax->getTranslation('name', 'en') ?? $tax->name ?? '';
            $taxNames['ar'][] = $tax->getTranslation('name', 'ar') ?? $tax->name ?? '';
        }
        $taxNameEn = implode(', ', array_filter($taxNames['en']));
        $taxNameAr = implode(', ', array_filter($taxNames['ar']));

        // Return formatted array for pipeline
        return [
            'id' => $vendorProduct->id,
            'product' => [
                'id' => $vendorProduct?->product?->id,
                'title' => $vendorProduct?->product?->title,
                'title_en' => $productNameEn,
                'title_ar' => $productNameAr,
                'translations' => $vendorProduct?->product?->translations->toArray(),
                'department' => [
                    'id' => $vendorProduct?->product?->department?->id,
                    'name' => $vendorProduct?->product?->department?->name,
                    'commission' => $vendorProduct?->product?->department?->commission,
                ],
                'category' => [
                    'id' => $vendorProduct?->product?->category?->id,
                    'name' => $vendorProduct?->product?->category?->name,
                ],
                'subCategory' => [
                    'id' => $vendorProduct?->product?->subCategory?->id,
                    'name' => $vendorProduct?->product?->subCategory?->name,
                ],
                'brand' => $vendorProduct?->product?->brand,
            ],
            'vendor' => [
                'id' => $vendorProduct?->vendor?->id,
                'name' => $vendorProduct?->vendor?->name,
            ],
            'price' => $vendorProduct->price,
            'taxes' => $taxes->map(function ($tax) {
                return [
                    'id' => $tax->id,
                    'name' => $tax->name,
                    'name_en' => $tax->getTranslation('name', 'en') ?? $tax->name,
                    'name_ar' => $tax->getTranslation('name', 'ar') ?? $tax->name,
                    'percentage' => $tax->percentage ?? 0,
                ];
            })->toArray(),
            'tax_rate' => $taxRate,
            'tax_name_en' => $taxNameEn,
            'tax_name_ar' => $taxNameAr,
            'max_per_order' => $vendorProduct->max_per_order,
            'variants' => $vendorProduct->variants,
        ];
    }

    /**
     * Get filters by occasion
     * TODO: Uncomment when Occasion model is created
     */
    // public function getFiltersByOccasion(array $filters)
    // {
    //     return [
    //         'brands' => $this->getBrandsByFilters($filters),
    //         'variants' => $this->getTreesByFilters($filters),
    //     ];
    // }

    /**
     * Get filters by bundle category
     * TODO: Uncomment when BundleCategory model is created
     */
    // public function getFiltersByBundleCategory(array $filters)
    // {
    //     return [
    //         'brands' => $this->getBrandsByFilters($filters),
    //         'variants' => $this->getTreesByFilters($filters),
    //     ];
    // }


    /**
     * Get price range from filtered products
     * Optimized to use whereIn with subquery
     */
    public function getPriceByFilters(array $filters)
    {
        // Build the base query with filters
        $baseQuery = $this->query->handle($filters);
        
        // Use whereIn with subquery to get max price directly
        $maxPrice = DB::table('vendor_product_variants')
            ->whereIn('vendor_product_id', function($query) use ($baseQuery) {
                $query->select('id')
                    ->fromSub($baseQuery, 'filtered_products');
            })
            ->where('price', '>', 0)
            ->max('price');

        return $maxPrice ?? 0;
    }

    /**
     * Get tags from filtered products
     * Optimized to use chunking and direct database queries to prevent memory exhaustion
     */
    public function getTagsByFilters(array $filters)
    {
        // Check if any product-related filter is provided
        $hasFilters = !empty($filters['department_id']) || 
                      !empty($filters['main_category_id']) || 
                      !empty($filters['category_id']) || 
                      !empty($filters['sub_category_id']) || 
                      !empty($filters['brand_id']) || 
                      !empty($filters['vendor_id']) ||
                      !empty($filters['search']) ||
                      !empty($filters['min_price']) ||
                      !empty($filters['max_price']);
        
        // Get filtered vendor products query
        $vendorProductQuery = $this->query->handle($filters);
        
        // Get product IDs using a subquery
        $productIdsQuery = DB::table('vendor_products')
            ->whereIn('id', $vendorProductQuery->select('id'))
            ->select('product_id');
        
        // Get tags from translations table directly using chunking
        // Tags are stored in translations table with lang_key = 'tags'
        $tags = [];
        $locale = app()->getLocale();
        $language = \App\Models\Language::where('code', $locale)->first();
        $langId = $language?->id;
        
        if (!$langId) {
            return [];
        }
        
        // Query translations table directly for tags using a subquery for product IDs
        \App\Models\Translation::where('translatable_type', \Modules\CatalogManagement\app\Models\Product::class)
            ->whereIn('translatable_id', $productIdsQuery)
            ->where('lang_key', 'tags')
            ->where('lang_id', $langId)
            ->whereNotNull('lang_value')
            ->where('lang_value', '!=', '')
            ->select('lang_value')
            ->chunkById(500, function ($translations) use (&$tags) {
                foreach ($translations as $translation) {
                    $productTags = explode(',', $translation->lang_value);
                    $productTags = array_map('trim', $productTags);
                    $tags = array_merge($tags, $productTags);
                }
            });

        return array_values(array_unique(array_filter($tags)));
    }

    /**
     * Get brands from filtered products
     */
    public function getBrandsByProductFilters(array $filters)
    {
        // Check if any product-related filter is provided
        $hasFilters = !empty($filters['department_id']) || 
                      !empty($filters['main_category_id']) || 
                      !empty($filters['category_id']) || 
                      !empty($filters['sub_category_id']) || 
                      !empty($filters['vendor_id']) ||
                      !empty($filters['brand_id']) ||
                      !empty($filters['search']) ||
                      !empty($filters['min_price']) ||
                      !empty($filters['max_price']);
        
        // If no filters, return all active brands
        if (!$hasFilters) {
            return \Modules\CatalogManagement\app\Models\Brand::where('active', true)
                ->with('logo')
                ->get()
                ->map(function ($brand) {
                    return [
                        'id' => $brand->id,
                        'title' => $brand->name,
                        'slug' => $brand->slug,
                        'image' => $brand->logo ? asset('storage/' . $brand->logo->path) : '',
                        'icon' => '',
                        'type' => 'brand',
                    ];
                })->toArray();
        }

        // Get filtered vendor products query using ProductQueryAction
        $vendorProductQuery = $this->query->handle($filters);
        
        // Get brand IDs directly using a subquery for better performance
        $brandIds = Product::whereIn('id', function($query) use ($vendorProductQuery) {
            $query->select('product_id')
                ->from('vendor_products')
                ->whereIn('id', $vendorProductQuery->select('id'));
        })
        ->whereNotNull('brand_id')
        ->pluck('brand_id')
        ->unique()
        ->toArray();
        
        if (empty($brandIds)) {
            return [];
        }

        return \Modules\CatalogManagement\app\Models\Brand::whereIn('id', $brandIds)
            ->where('active', true)
            ->with('logo')
            ->get()
            ->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'title' => $brand->name,
                    'slug' => $brand->slug,
                    'image' => $brand->logo ? asset('storage/' . $brand->logo->path) : '',
                    'icon' => '',
                    'type' => 'brand',
                ];
            })->toArray();
    }

    /**
     * Get variant trees from filtered products
     */
    public function getTreesByFilters(array $filters)
    {
        // Check if any product-related filter is provided
        $hasFilters = !empty($filters['department_id']) || 
                      !empty($filters['main_category_id']) || 
                      !empty($filters['category_id']) || 
                      !empty($filters['sub_category_id']) || 
                      !empty($filters['brand_id']) || 
                      !empty($filters['vendor_id']) ||
                      !empty($filters['occasion_id']) || 
                      !empty($filters['bundle_category_id']) ||
                      !empty($filters['search']) ||
                      !empty($filters['min_price']) ||
                      !empty($filters['max_price']);
        
        // If no filters, return all trees
        if (!$hasFilters) {
            return VariantConfigurationKey::query()
                ->whereNull('parent_key_id')
                ->with([
                    'variants' => function ($q) {
                        $q->whereNull('parent_id')
                            ->with(['childrenRecursive', 'childrenRecursive.key', 'childrenRecursive.translations', 'childrenRecursive.key.translations']);
                    },
                    'childrenKeys' => function ($q) {
                        $q->with([
                            'variants' => function ($q2) {
                                $q2->whereNull('parent_id')
                                    ->with(['childrenRecursive', 'childrenRecursive.key']);
                            },
                            'childrenKeys'
                        ]);
                    }
                ])
                ->get();
        }

        // Get filtered vendor products using ProductQueryAction
        $query = $this->query->handle($filters);
        $vendorProductIds = $query->pluck('id')->toArray();
        
        // If no products match the filters, return empty collection
        if (empty($vendorProductIds)) {
            return collect([]);
        }

        // Get variant configuration IDs from filtered products' variants
        $configIds = \Modules\CatalogManagement\app\Models\VendorProductVariant::whereIn('vendor_product_id', $vendorProductIds)
            ->whereNotNull('variant_configuration_id')
            ->pluck('variant_configuration_id')
            ->unique()
            ->toArray();

        if (empty($configIds)) {
            return collect([]);
        }

        // Get key IDs from configurations
        $keyIds = \Modules\CatalogManagement\app\Models\VariantsConfiguration::whereIn('id', $configIds)
            ->pluck('key_id')
            ->unique()
            ->toArray();
        
        if (empty($keyIds)) {
            return collect([]);
        }

        // Get trees with only the relevant variants
        return VariantConfigurationKey::query()
            ->whereIn('id', $keyIds)
            ->whereNull('parent_key_id')
            ->with([
                'variants' => function ($q) use ($configIds) {
                    $q->whereNull('parent_id')
                        ->whereIn('id', $configIds)
                        ->with(['childrenRecursive', 'childrenRecursive.key', 'childrenRecursive.translations', 'childrenRecursive.key.translations']);
                },
                'childrenKeys' => function ($q) use ($configIds) {
                    $q->with([
                        'variants' => function ($q2) use ($configIds) {
                            $q2->whereNull('parent_id')
                                ->whereIn('id', $configIds)
                                ->with(['childrenRecursive', 'childrenRecursive.key']);
                        },
                        'childrenKeys'
                    ]);
                }
            ])
            ->get();
    }

    public function getVariantsWithProduct(array $filters)
    {
        // Use a lightweight query for order creation - skip the heavy ProductQueryAction
        $query = VendorProduct::query()
            ->active()
            ->status(VendorProduct::STATUS_APPROVED)
            ->filter($filters);
    
        // Note: category, department, subCategory are on Product, not VendorProduct
        $query->with([
            'product.translations',
            'product.category.translations',
            'product.department.translations',
            'product.subCategory.translations',
            'variants.variantConfiguration.key',
            'variants.stocks',
            'vendor.translations',
            'taxes.translations'
        ]);
        return $query->paginate(10);
    }

    /**
     * Get product by slug with all vendors, prices, and stock
     */
    public function getProductBySlug(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->with(['translations', 'brand', 'category', 'subCategory'])
            ->first();

        if (!$product) {
            return null;
        }

        // Get all vendors selling this product with their variants and stock
        $vendorProducts = $product->vendorProducts()
            ->where('is_active', true)
            ->with([
                'vendor',
                'variants' => function($query) {
                    $query->with([
                        'stocks.region',
                        'variantConfiguration.parent_data.key',
                        'variantConfiguration.key'
                    ]);
                }
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'star') // assumes your reviews table has a 'star' column
            ->orderBy('sort_number')
            ->get()
            ;

        $reviews =[];
        $totalReviews = $vendorProducts->sum('reviews_count');
        $reviews['total_reviews'] = $totalReviews;
        // Calculate weighted average star rating
        $totalStars = $vendorProducts->sum(function ($vp) {
            return $vp->reviews_count * $vp->reviews_avg_star;
        });
        $avgStar = $totalReviews > 0 ? round($totalStars / $totalReviews, 2) : null;
        $reviews['avg_star'] = $avgStar;
        return [
            'product' => $product,
            'vendorProducts' => $vendorProducts,
            'reviews' => $reviews,
        ];
    }

    /**
     * Get filters (trees and brands) by occasion products
     */
    public function getFiltersByOccasion(array $filters)
    {
        // Get vendor product variant IDs from occasion products
        $query = \Modules\CatalogManagement\app\Models\OccasionProduct::query()
            ->whereHas('occasion', function ($q) use ($filters) {
                $q->where('is_active', true)
                  ->where('end_date', '>=', now()->toDateString());
                
                // Filter by specific occasion if provided
                if (!empty($filters['occasion_id'])) {
                    $q->where('id', $filters['occasion_id']);
                }
            });
        
        $variantIds = $query->pluck('vendor_product_variant_id')->unique()->toArray();
        
        // Get vendor product IDs from variants
        $vendorProductIds = \Modules\CatalogManagement\app\Models\VendorProductVariant::whereIn('id', $variantIds)
            ->pluck('vendor_product_id')
            ->unique()
            ->toArray();
        
        // Get product IDs from vendor products
        $productQuery = \Modules\CatalogManagement\app\Models\VendorProduct::whereIn('id', $vendorProductIds);
        
        // Filter by brand if provided
        if (!empty($filters['brand_id'])) {
            $productQuery->whereHas('product', function ($q) use ($filters) {
                $q->where('brand_id', $filters['brand_id']);
            });
        }
        
        $productIds = $productQuery->pluck('product_id')->unique()->toArray();
        
        // Get brands from products
        $brandQuery = Product::whereIn('id', $productIds)->whereNotNull('brand_id');
        
        // If brand_id filter is applied, only return that brand
        if (!empty($filters['brand_id'])) {
            $brandQuery->where('brand_id', $filters['brand_id']);
        }
        
        $brandIds = $brandQuery->pluck('brand_id')->unique()->toArray();
        
        $brands = \Modules\CatalogManagement\app\Models\Brand::whereIn('id', $brandIds)
            ->get()
            ->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'slug' => $brand->slug,
                    'name' => $brand->name,
                    'image' => $brand->image ? asset('storage/' . $brand->image) : null,
                ];
            })
            ->toArray();
        
        // Get variant IDs filtered by brand if needed
        if (!empty($filters['brand_id'])) {
            $filteredVendorProductIds = \Modules\CatalogManagement\app\Models\VendorProduct::whereIn('product_id', $productIds)
                ->pluck('id')
                ->toArray();
            
            $variantIds = \Modules\CatalogManagement\app\Models\VendorProductVariant::whereIn('vendor_product_id', $filteredVendorProductIds)
                ->pluck('id')
                ->toArray();
        }
        
        // Get variant configuration IDs from variants
        $configIds = \Modules\CatalogManagement\app\Models\VendorProductVariant::whereIn('id', $variantIds)
            ->whereNotNull('variant_configuration_id')
            ->pluck('variant_configuration_id')
            ->unique()
            ->toArray();
        
        // Get trees from configurations
        $trees = $this->getTreesFromConfigIds($configIds);
        
        return [
            'trees' => $trees,
            'brands' => $brands,
        ];
    }

    /**
     * Get filters (trees and brands) by bundle products
     */
    public function getFiltersByBundle(array $filters)
    {
        // Get vendor product variant IDs from bundle products
        $query = \Modules\CatalogManagement\app\Models\BundleProduct::query()
            ->whereHas('bundle', function ($q) use ($filters) {
                $q->where('is_active', true)
                  ->where('admin_approval', 1);
                
                // Filter by specific bundle category if provided
                if (!empty($filters['bundle_category_id'])) {
                    $q->where('bundle_category_id', $filters['bundle_category_id']);
                }
            });
        
        $variantIds = $query->pluck('vendor_product_variant_id')->unique()->toArray();
        
        // Get vendor product IDs from variants
        $vendorProductIds = \Modules\CatalogManagement\app\Models\VendorProductVariant::whereIn('id', $variantIds)
            ->pluck('vendor_product_id')
            ->unique()
            ->toArray();
        
        // Get product IDs from vendor products
        $productQuery = \Modules\CatalogManagement\app\Models\VendorProduct::whereIn('id', $vendorProductIds);
        
        // Filter by brand if provided
        if (!empty($filters['brand_id'])) {
            $productQuery->whereHas('product', function ($q) use ($filters) {
                $q->where('brand_id', $filters['brand_id']);
            });
        }
        
        $productIds = $productQuery->pluck('product_id')->unique()->toArray();
        
        // Get brands from products
        $brandQuery = Product::whereIn('id', $productIds)->whereNotNull('brand_id');
        
        // If brand_id filter is applied, only return that brand
        if (!empty($filters['brand_id'])) {
            $brandQuery->where('brand_id', $filters['brand_id']);
        }
        
        $brandIds = $brandQuery->pluck('brand_id')->unique()->toArray();
        
        $brands = \Modules\CatalogManagement\app\Models\Brand::whereIn('id', $brandIds)
            ->get()
            ->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'slug' => $brand->slug,
                    'name' => $brand->name,
                    'image' => $brand->image ? asset('storage/' . $brand->image) : null,
                ];
            })
            ->toArray();
        
        // Get variant IDs filtered by brand if needed
        if (!empty($filters['brand_id'])) {
            $filteredVendorProductIds = \Modules\CatalogManagement\app\Models\VendorProduct::whereIn('product_id', $productIds)
                ->pluck('id')
                ->toArray();
            
            $variantIds = \Modules\CatalogManagement\app\Models\VendorProductVariant::whereIn('vendor_product_id', $filteredVendorProductIds)
                ->pluck('id')
                ->toArray();
        }
        
        // Get variant configuration IDs from variants
        $configIds = \Modules\CatalogManagement\app\Models\VendorProductVariant::whereIn('id', $variantIds)
            ->whereNotNull('variant_configuration_id')
            ->pluck('variant_configuration_id')
            ->unique()
            ->toArray();
        
        // Get trees from configurations
        $trees = $this->getTreesFromConfigIds($configIds);
        
        return [
            'trees' => $trees,
            'brands' => $brands,
        ];
    }

    /**
     * Get variant configuration trees from configuration IDs
     */
    private function getTreesFromConfigIds(array $configIds)
    {
        if (empty($configIds)) {
            return [];
        }
        
        // Get key IDs from configurations
        $keyIds = \Modules\CatalogManagement\app\Models\VariantsConfiguration::whereIn('id', $configIds)
            ->pluck('key_id')
            ->unique()
            ->toArray();
        
        // Get trees with only the relevant variants
        return VariantConfigurationKey::query()
            ->whereIn('id', $keyIds)
            ->orWhereHas('variants', function ($q) use ($configIds) {
                $q->whereIn('id', $configIds);
            })
            ->whereNull('parent_key_id')
            ->with([
                'variants' => function ($q) use ($configIds) {
                    $q->whereNull('parent_id')
                        ->where(function ($subQ) use ($configIds) {
                            $subQ->whereIn('id', $configIds)
                                ->orWhereHas('childrenRecursive', function ($childQ) use ($configIds) {
                                    $childQ->whereIn('id', $configIds);
                                });
                        })
                        ->with(['childrenRecursive', 'childrenRecursive.key', 'childrenRecursive.translations', 'childrenRecursive.key.translations']);
                },
                'childrenKeys' => function ($q) use ($configIds) {
                    $q->with([
                        'variants' => function ($q2) use ($configIds) {
                            $q2->whereNull('parent_id')
                                ->whereIn('id', $configIds)
                                ->with(['childrenRecursive', 'childrenRecursive.key']);
                        },
                        'childrenKeys'
                    ]);
                }
            ])
            ->get()
            ->map(function ($key) {
                return [
                    'id' => $key->id,
                    'name' => $key->name,
                    'variants' => $key->variants->map(function ($variant) {
                        return [
                            'id' => $variant->id,
                            'name' => $variant->name,
                            'color' => $variant->color,
                            'children' => $variant->childrenRecursive->map(function ($child) {
                                return [
                                    'id' => $child->id,
                                    'name' => $child->name,
                                    'color' => $child->color,
                                ];
                            })->toArray(),
                        ];
                    })->toArray(),
                ];
            })
            ->toArray();
    }

    /**
     * Get available product IDs from a list of IDs
     * Returns only the IDs that exist in the database
     * 
     * @param array $productIds
     * @return array
     */
    public function getAvailableProductIds(array $productIds)
    {
        return VendorProduct::whereIn('id', $productIds)
            ->active()
            ->status(VendorProduct::STATUS_APPROVED)
            ->pluck('id')
            ->toArray();
    }
}
