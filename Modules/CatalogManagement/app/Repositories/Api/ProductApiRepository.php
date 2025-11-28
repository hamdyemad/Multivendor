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
use Modules\CategoryManagment\app\Services\Api\CategoryApiService;

class ProductApiRepository implements ProductApiRepositoryInterface
{
    public function __construct(
        private ProductQueryAction $query,
        private IsPaginatedAction $paginated,
        private CategoryApiService $categoryService
    ) {}

    /**
     * Get all products with filtering and pagination
     */
    public function getAllProducts(ProductFilterDTO $dto)
    {
        $filters = $dto->toArray();
        $query = $this->query->handle($filters);
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

    /**
     * Get hot deals
     */
    public function getHotDeals(ProductFilterDTO $filters)
    {
        $limit = $filters['limit'] ?? 3;
        $filters['has_discount'] = true;
        $query = $this->query->handle($filters);
        return $query->limit($limit)->get();
    }

    /**
     * Get top products
     */
    public function getTopProducts(ProductFilterDTO $filters)
    {
        $limit = $filters['limit'] ?? 3;
        $filters['sort_by'] = 'sales';
        $query = $this->query->handle($filters);
        return $query->limit($limit)->get();
    }

    /**
     * Get product variants keys
     */
    public function getProductVariantsKeys(string $productId)
    {
        return Product::query()
            ->where('id', $productId)
            ->with([
                'variants.configuration.key',
                'variants.configuration.parentData.key',
                'variants.configuration.parentData.parentData.key',
            ])
            ->first();
    }

    /**
     * Store product review
     */
    public function storeProductReview(array $data)
    {
        // TODO: Implement product review storage
        // This should create a review record for the product
        return null;
    }

    /**
     * Get filters (categories, brands, variants, etc.)
     */
    public function getFilters(array $filters)
    {
        return [
            'categories' => $this->getCategoriesByFilters($filters),
            'brands' => $this->getBrandsByFilters($filters),
            'price_range' => $this->getPriceByFilters($filters),
            'tags' => $this->getTagsByFilters($filters),
            'inputs' => $this->getInputsByFilters($filters),
            'variants' => $this->getTreesByFilters($filters),
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
     * Get categories based on filters
     */
    public function getCategoriesByFilters(array $filters)
    {
        return $this->categoryService->getCategoriesByFilters($filters);
    }

    /**
     * Get brands based on filters
     */
    public function getBrandsByFilters(array $filters)
    {
        if (!empty($filters['brand_id'])) {
            return collect();
        }

        $query = Brand::query()
            ->whereHas('products', function ($q) use ($filters) {
                $q->where('is_active', true);

                // Apply category filters
                if (!empty($filters['department_id'])) {
                    $q->where('department_id', $filters['department_id']);
                }

                if (!empty($filters['category_id'])) {
                    $q->where('category_id', $filters['category_id']);
                }

                if (!empty($filters['sub_category_id'])) {
                    $q->where('sub_category_id', $filters['sub_category_id']);
                }
            });

        return $query->get();
    }

    /**
     * Get price range from filtered products
     */
    public function getPriceByFilters(array $filters)
    {
        $query = $this->query->handle($filters)
            ->whereHas('variants');

        $maxPrice = $query->max(DB::raw('(SELECT MAX(price) FROM variants_configurations WHERE product_id = products.id)'));

        return [
            'min' => 0,
            'max' => $maxPrice ?? 0,
        ];
    }

    /**
     * Get tags from filtered products
     */
    public function getTagsByFilters(array $filters)
    {
        $query = $this->query->handle($filters);

        $tags = [];
        foreach ($query->get() as $product) {
            if ($product->tags) {
                $tags = array_merge($tags, explode(',', $product->tags));
            }
        }

        return array_unique($tags);
    }

    /**
     * Get inputs from filtered products
     */
    public function getInputsByFilters(array $filters)
    {
        // This would depend on your input structure
        // For now, returning empty array
        return [];
    }

    /**
     * Get variant trees from filtered products
     */
    public function getTreesByFilters(array $filters)
    {
        return VariantConfigurationKey::query()
            ->whereNull('parent_id')
            ->with([
                'variants' => function ($q) {
                    $q->whereNull('parent_id')
                        ->with('childrenRecursive.key');
                }
            ])
            ->get();
    }
}
