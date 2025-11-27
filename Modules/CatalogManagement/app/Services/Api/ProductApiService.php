<?php

namespace Modules\CatalogManagement\app\Services\Api;

use Modules\CatalogManagement\app\Interfaces\Api\ProductApiRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ProductApiService
{
    public function __construct(
        protected ProductApiRepositoryInterface $repository
    ) {}

    /**
     * Get all products with filtering and pagination
     */
    public function getAllProducts(array $filters)
    {
        return $this->repository->getAllProducts($filters);
    }

    /**
     * Get products by department
     */
    public function getProductsByDepartment(string $departmentId, array $filters)
    {
        return $this->repository->getProductsByDepartment($departmentId, $filters);
    }

    /**
     * Get specific product by ID or slug
     */
    public function getProductByIdOrSlug(string $identifier, array $filters = [])
    {
        $product = $this->repository->getProductByIdOrSlug($identifier, $filters);

        if ($product) {
            // Increment views
            $this->repository->incrementProductViews($product->id);
        }

        return $product;
    }

    /**
     * Search products
     */
    public function searchProducts(string $query, array $filters)
    {
        return $this->repository->searchProducts($query, $filters);
    }

    /**
     * Get featured products
     */
    public function getFeaturedProducts(array $filters)
    {
        return $this->repository->getFeaturedProducts($filters);
    }

    /**
     * Get best selling products
     */
    public function getBestSellingProducts(array $filters)
    {
        return $this->repository->getBestSellingProducts($filters);
    }

    /**
     * Get latest products
     */
    public function getLatestProducts(array $filters)
    {
        return $this->repository->getLatestProducts($filters);
    }

    /**
     * Get special offer products
     */
    public function getSpecialOfferProducts(array $filters)
    {
        return $this->repository->getSpecialOfferProducts($filters);
    }

    /**
     * Get hot deals
     */
    public function getHotDeals(array $filters)
    {
        return $this->repository->getHotDeals($filters);
    }

    /**
     * Get top products
     */
    public function getTopProducts(array $filters)
    {
        return $this->repository->getTopProducts($filters);
    }

    /**
     * Get star products
     */
    public function getStarProducts(array $filters)
    {
        return $this->repository->getStarProducts($filters);
    }

    /**
     * Get product variants keys
     */
    public function getProductVariantsKeys(string $productId)
    {
        return $this->repository->getProductVariantsKeys($productId);
    }

    /**
     * Store product review
     */
    public function storeProductReview(string $productId, array $data)
    {
        $data['product_id'] = $productId;
        $data['customer_id'] = Auth::id();

        return $this->repository->storeProductReview($data);
    }

    /**
     * Get filters (categories, brands, variants, etc.)
     */
    public function getFilters(array $filters)
    {
        return $this->repository->getFilters($filters);
    }

    /**
     * Get filters by occasion
     * TODO: Uncomment when Occasion model is created
     */
    // public function getFiltersByOccasion(array $filters)
    // {
    //     return $this->repository->getFiltersByOccasion($filters);
    // }

    /**
     * Get filters by bundle category
     * TODO: Uncomment when BundleCategory model is created
     */
    // public function getFiltersByBundleCategory(array $filters)
    // {
    //     return $this->repository->getFiltersByBundleCategory($filters);
    // }

    /**
     * Get categories based on filters
     */
    public function getCategoriesByFilters(array $filters)
    {
        return $this->repository->getCategoriesByFilters($filters);
    }

    /**
     * Get brands based on filters
     */
    public function getBrandsByFilters(array $filters)
    {
        return $this->repository->getBrandsByFilters($filters);
    }

    /**
     * Get price range from filtered products
     */
    public function getPriceByFilters(array $filters)
    {
        return $this->repository->getPriceByFilters($filters);
    }

    /**
     * Get tags from filtered products
     */
    public function getTagsByFilters(array $filters)
    {
        return $this->repository->getTagsByFilters($filters);
    }

    /**
     * Get inputs from filtered products
     */
    public function getInputsByFilters(array $filters)
    {
        return $this->repository->getInputsByFilters($filters);
    }

    /**
     * Get variant trees from filtered products
     */
    public function getTreesByFilters(array $filters)
    {
        return $this->repository->getTreesByFilters($filters);
    }

    /**
     * Count sold products
     */
    public function countSoldProducts(string $productId, ?string $variantId = null)
    {
        return $this->repository->countSoldProducts($productId, $variantId);
    }
}
