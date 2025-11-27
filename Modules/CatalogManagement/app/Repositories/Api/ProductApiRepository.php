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
    public function getAllProducts(array $filters)
    {
        $paginated = isset($filters["paginated"]) ? true : false;
        $query = $this->query->handle($filters);
        $result = $this->paginated->handle($query, $paginated, $filters["per_page"] ?? null);
        return $result;
    }

    /**
     * Get products by department
     */
    public function getProductsByDepartment(string $departmentId, array $filters)
    {
        $filters['department_id'] = $departmentId;
        return $this->getAllProducts($filters);
    }

    /**
     * Get specific product by ID or slug
     */
    public function getProductByIdOrSlug(string $identifier, array $filters = [])
    {
        $query = $this->query->handle($filters);

        return $query->where(function ($q) use ($identifier) {
                $q->where('id', $identifier)
                    ->orWhere('slug', $identifier);
            })
            // ->with([
            //     'approvedReviews'
            // ])
            ->first();
    }

    /**
     * Get featured products
     */
    public function getFeaturedProducts(array $filters)
    {
        $filters['featured'] = true;
        return $this->getAllProducts($filters);
    }

    /**
     * Get best selling products
     */
    public function getBestSellingProducts(array $filters)
    {
        $filters['sort_by'] = 'sales';
        return $this->getAllProducts($filters);
    }

    /**
     * Get latest products
     */
    public function getLatestProducts(array $filters)
    {
        $filters['sort_by'] = 'created_at';
        return $this->getAllProducts($filters);
    }

    /**
     * Get special offer products
     */
    public function getSpecialOfferProducts(array $filters)
    {
        // This would need a special scope in the model
        $filters['has_discount'] = true;
        return $this->getAllProducts($filters);
    }

    /**
     * Get hot deals
     */
    public function getHotDeals(array $filters)
    {
        $limit = $filters['limit'] ?? 3;
        $filters['has_discount'] = true;
        $query = $this->query->handle($filters);
        return $query->limit($limit)->get();
    }

    /**
     * Get top products
     */
    public function getTopProducts(array $filters)
    {
        $limit = $filters['limit'] ?? 3;
        $filters['sort_by'] = 'sales';
        $query = $this->query->handle($filters);
        return $query->limit($limit)->get();
    }

    /**
     * Get star products
     */
    public function getStarProducts(array $filters)
    {
        $filters['sort_by'] = 'rating';
        return $this->getAllProducts($filters);
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

    /**
     * Count sold products
     */
    public function countSoldProducts(string $productId, ?string $variantId = null)
    {
        // This would depend on your order structure
        // For now, returning 0
        return 0;
    }

    /**
     * Increment product views
     */
    public function incrementProductViews(string $productId)
    {
        Product::query()
            ->where('id', $productId)
            ->increment('views');
    }

}
