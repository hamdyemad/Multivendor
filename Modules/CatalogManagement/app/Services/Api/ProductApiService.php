<?php

namespace Modules\CatalogManagement\app\Services\Api;

use Modules\CatalogManagement\app\Interfaces\Api\ProductApiRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Modules\CatalogManagement\app\DTOs\BrandFilterDTO;
use Modules\CatalogManagement\app\DTOs\ProductFilterDTO;
use Modules\CategoryManagment\app\Services\Api\CategoryApiService;

class ProductApiService
{
    public function __construct(
        protected ProductApiRepositoryInterface $repository,
        protected CategoryApiService $CategoryService,
        protected BrandApiService $BrandService
    ) {}

    /**
     * Get all products with filtering and pagination
     */
    public function getAllProducts(ProductFilterDTO $dto)
    {
        return $this->repository->getAllProducts($dto);
    }

    /**
     * Get specific product by ID or slug
     */
    public function getProductByIdOrSlug(string $identifier, string $vendorId)
    {
        $product = $this->repository->getProductByIdOrSlug($identifier, $vendorId);

        if ($product) {
            // Increment views
            $this->repository->incrementProductViews($product->id);
        }

        return $product;
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
        $brandDto = new BrandFilterDTO(department_id: $filters['department_id'] ?? null, category_id: $filters['category_id'] ?? null, sub_category_id: $filters['sub_category_id'] ?? null);

        return [
            'category_info' => $this->CategoryService->getCategoriesByIds($filters),
            'categories' => $this->CategoryService->getCategoriesByFilters($filters),
            'brands' => $this->BrandService->getAllBrands($brandDto),
            'price' => $this->repository->getPriceByFilters($filters),
            'tags' => $this->repository->getTagsByFilters($filters),
            'trees' => $this->repository->getTreesByFilters($filters),
        ];
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
     * Get variant trees from filtered products
     */
    public function getTreesByFilters(array $filters)
    {
        return $this->repository->getTreesByFilters($filters);
    }
}
