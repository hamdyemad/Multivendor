<?php

namespace Modules\CatalogManagement\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\CatalogManagement\app\Services\Api\ProductApiService;
use Modules\CatalogManagement\app\Http\Resources\Api\ProductResource;
use Modules\CatalogManagement\app\Http\Requests\Api\ProductReviewRequest;

class ProductApiController extends Controller
{
    use Res;

    public function __construct(
        protected ProductApiService $productService
    ) {}

    /**
     * Get all products with filtering and pagination
     * GET /api/products
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        $products = $this->productService->getAllProducts($filters);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            ProductResource::collection($products),
            [],
            200
        );
    }

    /**
     * Get products by department
     * GET /api/products/department/{id}
     */
    public function getByDepartment(Request $request, string $departmentId)
    {
        $filters = $request->all();
        $products = $this->productService->getProductsByDepartment($departmentId, $filters);

        return $this->sendRes(
            config('responses.success'),
            true,
            ProductResource::collection($products),
            [],
            200
        );
    }

    /**
     * Get specific product by ID or slug
     * GET /api/products/{id}
     */
    public function show(string $identifier)
    {
        $product = $this->productService->getProductByIdOrSlug($identifier);

        if (!$product) {
            return $this->sendRes(
                config('responses.product_not_found'),
                false,
                [],
                [],
                404
            );
        }

        return $this->sendRes(
            config('responses.success'),
            true,
            new ProductResource($product),
            [],
            200
        );
    }

    /**
     * Search products
     * GET /api/products/search
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $query = $request->input('q');
        $filters = $request->all();
        $products = $this->productService->searchProducts($query, $filters);

        return $this->sendRes(
            config('responses.success'),
            true,
            ProductResource::collection($products),
            [],
            200
        );
    }

    /**
     * Get featured products
     * GET /api/products/featured
     */
    public function featured(Request $request)
    {
        $filters = $request->all();
        $products = $this->productService->getFeaturedProducts($filters);

        return $this->sendRes(
            config('responses.success'),
            true,
            ProductResource::collection($products),
            [],
            200
        );
    }

    /**
     * Get best selling products
     * GET /api/products/best-selling
     */
    public function bestSelling(Request $request)
    {
        $filters = $request->all();
        $products = $this->productService->getBestSellingProducts($filters);

        return $this->sendRes(
            config('responses.success'),
            true,
            ProductResource::collection($products),
            [],
            200
        );
    }

    /**
     * Get latest products
     * GET /api/products/latest
     */
    public function latest(Request $request)
    {
        $filters = $request->all();
        $products = $this->productService->getLatestProducts($filters);

        return $this->sendRes(
            config('responses.success'),
            true,
            ProductResource::collection($products),
            [],
            200
        );
    }

    /**
     * Get special offer products
     * GET /api/products/special-offers
     */
    public function specialOffers(Request $request)
    {
        $filters = $request->all();
        $products = $this->productService->getSpecialOfferProducts($filters);

        return $this->sendRes(
            config('responses.success'),
            true,
            ProductResource::collection($products),
            [],
            200
        );
    }

    /**
     * Get hot deals
     * GET /api/products/hot-deals
     */
    public function hotDeals(Request $request)
    {
        $filters = $request->all();
        $products = $this->productService->getHotDeals($filters);

        return $this->sendRes(
            config('responses.success'),
            true,
            ProductResource::collection($products),
            [],
            200
        );
    }

    /**
     * Get top products
     * GET /api/products/top
     */
    public function top(Request $request)
    {
        $filters = $request->all();
        $products = $this->productService->getTopProducts($filters);

        return $this->sendRes(
            config('responses.success'),
            true,
            ProductResource::collection($products),
            [],
            200
        );
    }

    /**
     * Get star products
     * GET /api/products/star
     */
    public function star(Request $request)
    {
        $filters = $request->all();
        $products = $this->productService->getStarProducts($filters);

        return $this->sendRes(
            config('responses.success'),
            true,
            ProductResource::collection($products),
            [],
            200
        );
    }

    /**
     * Get product variants keys
     * GET /api/products/{id}/variants-keys
     */
    public function variantsKeys(string $productId)
    {
        $product = $this->productService->getProductVariantsKeys($productId);

        if (!$product) {
            return $this->sendRes(
                config('responses.product_not_found'),
                false,
                [],
                [],
                404
            );
        }

        return $this->sendRes(
            config('responses.success'),
            true,
            new ProductResource($product),
            [],
            200
        );
    }

    /**
     * Store product review
     * POST /api/products/{id}/reviews
     */
    public function storeReview(ProductReviewRequest $request, string $productId)
    {
        if (!Auth::check()) {
            return $this->sendRes(
                config('responses.unauthorized'),
                false,
                [],
                [],
                401
            );
        }

        $validated = $request->validated();
        $review = $this->productService->storeProductReview($productId, $validated);

        return $this->sendRes(
            config('responses.review_sent_successfully'),
            true,
            $review,
            [],
            201
        );
    }

    /**
     * Get filters (categories, brands, variants, etc.)
     * GET /api/products/filters
     */
    public function filters(Request $request)
    {
        $filters = $request->all();
        $filterData = $this->productService->getFilters($filters);

        return $this->sendRes(
            config('responses.success'),
            true,
            $filterData,
            [],
            200
        );
    }

    /**
     * Get categories by filters
     * GET /api/products/categories
     */
    public function categories(Request $request)
    {
        $filters = $request->all();
        $categories = $this->productService->getCategoriesByFilters($filters);

        return $this->sendRes(
            config('responses.success'),
            true,
            $categories,
            [],
            200
        );
    }

    /**
     * Get brands by filters
     * GET /api/products/brands
     */
    public function brands(Request $request)
    {
        $filters = $request->all();
        $brands = $this->productService->getBrandsByFilters($filters);

        return $this->sendRes(
            config('responses.success'),
            true,
            $brands,
            [],
            200
        );
    }

    /**
     * Get price range by filters
     * GET /api/products/price-range
     */
    public function priceRange(Request $request)
    {
        $filters = $request->all();
        $priceRange = $this->productService->getPriceByFilters($filters);

        return $this->sendRes(
            config('responses.success'),
            true,
            $priceRange,
            [],
            200
        );
    }

    /**
     * Get tags by filters
     * GET /api/products/tags
     */
    public function tags(Request $request)
    {
        $filters = $request->all();
        $tags = $this->productService->getTagsByFilters($filters);

        return $this->sendRes(
            config('responses.success'),
            true,
            $tags,
            [],
            200
        );
    }

    /**
     * Get inputs by filters
     * GET /api/products/inputs
     */
    public function inputs(Request $request)
    {
        $filters = $request->all();
        $inputs = $this->productService->getInputsByFilters($filters);

        return $this->sendRes(
            config('responses.success'),
            true,
            $inputs,
            [],
            200
        );
    }

    /**
     * Get variant trees by filters
     * GET /api/products/variants
     */
    public function variants(Request $request)
    {
        $filters = $request->all();
        $variants = $this->productService->getTreesByFilters($filters);

        return $this->sendRes(
            config('responses.success'),
            true,
            $variants,
            [],
            200
        );
    }

    /**
     * Count sold products
     * GET /api/products/{id}/sold-count
     */
    public function soldCount(string $productId, Request $request)
    {
        $variantId = $request->input('variant_id');
        $count = $this->productService->countSoldProducts($productId, $variantId);

        return $this->sendRes(
            config('responses.success'),
            true,
            ['count' => $count],
            [],
            200
        );
    }
}
