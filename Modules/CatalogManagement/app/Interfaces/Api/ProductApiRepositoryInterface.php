<?php

namespace Modules\CatalogManagement\app\Interfaces\Api;

use Modules\CatalogManagement\app\DTOs\ProductFilterDTO;

interface ProductApiRepositoryInterface
{
    public function getAllProducts(ProductFilterDTO $filters);
    public function getProductByIdOrSlug(string $identifier, string $vendorId);
    public function getHotDeals(ProductFilterDTO $filters);
    public function getTopProducts(ProductFilterDTO $filters);
    public function getProductVariantsKeys(string $productId);
    public function storeProductReview(array $data);
    public function getFilters(array $filters);
    public function getCategoriesByFilters(array $filters);
    public function getBrandsByFilters(array $filters);
    public function getPriceByFilters(array $filters);
    public function getTagsByFilters(array $filters);
    public function getInputsByFilters(array $filters);
    public function getTreesByFilters(array $filters);
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
