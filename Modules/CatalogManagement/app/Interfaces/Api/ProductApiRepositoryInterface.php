<?php

namespace Modules\CatalogManagement\app\Interfaces\Api;

interface ProductApiRepositoryInterface
{
    public function getAllProducts(array $filters);
    public function getProductsByDepartment(string $departmentId, array $filters);
    public function getProductByIdOrSlug(string $identifier, array $filters = []);
    public function getFeaturedProducts(array $filters);
    public function getBestSellingProducts(array $filters);
    public function getLatestProducts(array $filters);
    public function getSpecialOfferProducts(array $filters);
    public function getHotDeals(array $filters);
    public function getTopProducts(array $filters);
    public function getStarProducts(array $filters);
    public function getProductVariantsKeys(string $productId);
    public function storeProductReview(array $data);
    public function getFilters(array $filters);
    public function getCategoriesByFilters(array $filters);
    public function getBrandsByFilters(array $filters);
    public function getPriceByFilters(array $filters);
    public function getTagsByFilters(array $filters);
    public function getInputsByFilters(array $filters);
    public function getTreesByFilters(array $filters);
    public function countSoldProducts(string $productId, ?string $variantId = null);
    public function incrementProductViews(string $productId);

    /**
     * Get filters by occasion
     * TODO: Uncomment when Occasion model is created
     */
    // public function getFiltersByOccasion(array $filters);

    /**
     * Get filters by bundle category
     * TODO: Uncomment when BundleCategory model is created
     */
    // public function getFiltersByBundleCategory(array $filters);


}
