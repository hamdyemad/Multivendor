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
        $query->with('highestDiscountVariant');
        $result = $this->paginated->handle($query, $dto->paginated, $dto->per_page);
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
                    $q->with(['variantConfiguration']);
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
        return $this->query->handle([])->where('id', $id)->first();
    }

    /**
     * Find vendor product with all relationships for order creation pipeline
     */
    public function findProductForOrder(string $id)
    {
        $vendorProduct = $this->query->handle([])
            ->where('id', $id)
            ->with([
                'product' => function ($q) {
                    $q->with([
                        'brand',
                        'department' => function ($q) {
                            $q->select('id', 'commission');
                        },
                        'category',
                        'subCategory',
                        'translations'
                    ]);
                },
                'vendor',
                'tax' => function ($q) {
                    $q->with('translations');
                },
                'variants' => function ($q) {
                    $q->with('variantConfiguration', 'stocks');
                }
            ])
            ->first();

        if (!$vendorProduct) {
            return null;
        }

        // Extract product translations using getTranslation method
        $productNameEn = $vendorProduct->product->getTranslation('title', 'en') ?? $vendorProduct->product->title;
        $productNameAr = $vendorProduct->product->getTranslation('title', 'ar') ?? $vendorProduct->product->title;

        // Extract tax translations using getTranslation method
        $taxNameEn = $vendorProduct->tax?->getTranslation('name', 'en') ?? ($vendorProduct->tax?->name ?? '');
        $taxNameAr = $vendorProduct->tax?->getTranslation('name', 'ar') ?? ($vendorProduct->tax?->name ?? '');

        // Return formatted array for pipeline
        return [
            'id' => $vendorProduct->id,
            'product' => [
                'id' => $vendorProduct->product->id,
                'title' => $vendorProduct->product->title,
                'title_en' => $productNameEn,
                'title_ar' => $productNameAr,
                'translations' => $vendorProduct->product->translations->toArray(),
                'department' => [
                    'id' => $vendorProduct->product->department->id,
                    'commission' => $vendorProduct->product->department->commission,
                ],
                'brand' => $vendorProduct->product->brand,
                'category' => $vendorProduct->product->category,
                'subCategory' => $vendorProduct->product->subCategory,
            ],
            'vendor' => [
                'id' => $vendorProduct->vendor->id,
                'name' => $vendorProduct->vendor->name,
            ],
            'price' => $vendorProduct->price,
            'tax' => [
                'id' => $vendorProduct->tax?->id,
                'name' => $vendorProduct->tax?->name,
                'name_en' => $taxNameEn,
                'name_ar' => $taxNameAr,
                'tax_rate' => $vendorProduct->tax?->tax_rate ?? 0,
            ],
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
     */
    public function getPriceByFilters(array $filters)
    {
        $query = $this->query->handle($filters)
            ->whereHas('variants');

        $maxPrice = $query->max(DB::raw('(SELECT MAX(price) FROM vendor_product_variants WHERE vendor_product_id = vendor_products.id)'));

        return $maxPrice ?? 0;
    }

    /**
     * Get tags from filtered products
     */
    public function getTagsByFilters(array $filters)
    {
        $products = $this->query->handle($filters)->get();
        $tags = [];
        foreach ($products as $product) {
            $productTags = $product->product->tags_array;
            $tags = array_merge($tags, $productTags);
        }

        return array_unique(array_filter($tags));
    }

    /**
     * Get variant trees from filtered products
     */
    public function getTreesByFilters(array $filters)
    {
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

    public function getVariantsWithProduct(array $filters)
    {
        $query = $this->query->handle($filters);
        $query->with([
            'product' => function ($subQ) {
                $subQ->with(['brand', 'department', 'category', 'subCategory']);
            },
            'variants' => function ($subQ) {
                $subQ->with(['variantConfiguration', 'stocks']);
            },
            'tax'
        ]);

        $result = $this->paginated->handle($query, $filters['paginated'] ?? false, $filters['per_page'] ?? 15);

        return $result;
    }

}
