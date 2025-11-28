<?php

namespace Modules\CatalogManagement\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\CatalogManagement\app\DTOs\ProductFilterDTO;
use Modules\CatalogManagement\app\Services\Api\ProductApiService;
use Modules\CatalogManagement\app\Http\Resources\Api\ProductResource;
use Modules\CatalogManagement\app\Http\Resources\Api\VendorProductResource;
use Modules\CatalogManagement\app\Http\Requests\Api\ProductReviewRequest;

class ProductApiController extends Controller
{
    use Res;

    public function __construct(
        protected ProductApiService $productService
    ) {}


    public function index(Request $request)
    {
        $dto = ProductFilterDTO::fromRequest($request);

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $vendorProducts = $this->productService->getAllProducts($dto);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            VendorProductResource::collection($vendorProducts),
            [],
            200
        );
    }

    public function getByDepartment(Request $request, string $departmentId)
    {
        $dto = ProductFilterDTO::fromRequest($request);
        $dto->department_id = $departmentId;
        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $products = $this->productService->getAllProducts($dto);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            VendorProductResource::collection($products),
            [],
            200
        );
    }

    /**
     * Get specific product by ID or slug
     * GET /api/products/{id}
     */
    public function show(string $identifier, string $vendorId)
    {
        $product = $this->productService->getProductByIdOrSlug($identifier, $vendorId);

        if (!$product) {
            return $this->sendRes(
                config('responses.product_not_found')[app()->getLocale()],
                false,
                [],
                [],
                404
            );
        }

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            new VendorProductResource($product),
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
        $dto = ProductFilterDTO::fromRequest($request);

        $dto->featured = true;
        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $products = $this->productService->getAllProducts($dto);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            VendorProductResource::collection($products),
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
        $dto = ProductFilterDTO::fromRequest($request);

        $dto->sort_by = 'sales';
        $dto->sort_type = 'desc';

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $products = $this->productService->getAllProducts($dto);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            VendorProductResource::collection($products),
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
        $dto = ProductFilterDTO::fromRequest($request);
        $dto->sort_by = 'created_at';
        $dto->sort_type = 'desc';
        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $products = $this->productService->getAllProducts($dto);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            VendorProductResource::collection($products),
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
        $dto = ProductFilterDTO::fromRequest($request);

        $dto->has_discount = true;

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $products = $this->productService->getAllProducts($dto);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            VendorProductResource::collection($products),
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
        $dto = ProductFilterDTO::fromRequest($request);

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $products = $this->productService->getAllProducts($dto);

        return $this->sendRes(
            config('responses.success'),
            true,
            VendorProductResource::collection($products),
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
        $dto = ProductFilterDTO::fromRequest($request);
        $dto->sort_by = 'sales';
        $dto->sort_type = 'desc';
        $dto->limit = 3;

        if (!$dto->validate()) {
            return $this->sendRes(
                config('responses.validation')[app()->getLocale()],
                false,
                [],
                $dto->getErrors(),
                422
            );
        }

        $products = $this->productService->getAllProducts($dto);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            VendorProductResource::collection($products),
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
        // $dto = ProductFilterDTO::fromRequest($request);

        // if (!$dto->validate()) {
        //     return $this->sendRes(
        //         config('responses.validation')[app()->getLocale()],
        //         false,
        //         [],
        //         $dto->getErrors(),
        //         422
        //     );
        // }

        $filterData = $this->productService->getFilters($request->all());

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
        // $dto = ProductFilterDTO::fromRequest($request);

        // if (!$dto->validate()) {
        //     return $this->sendRes(
        //         config('responses.validation')[app()->getLocale()],
        //         false,
        //         null,
        //         $dto->getErrors(),
        //         422
        //     );
        // }

        $categories = $this->productService->getCategoriesByFilters($request->all());

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
        // $dto = ProductFilterDTO::fromRequest($request);

        // if (!$dto->validate()) {
        //     return $this->sendRes(
        //         config('responses.validation')[app()->getLocale()],
        //         false,
        //         null,
        //         $dto->getErrors(),
        //         422
        //     );
        // }

        $brands = $this->productService->getBrandsByFilters($request->all());

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
        // $dto = ProductFilterDTO::fromRequest($request);

        // if (!$dto->validate()) {
        //     return $this->sendRes(
        //         config('responses.validation')[app()->getLocale()],
        //         false,
        //         null,
        //         $dto->getErrors(),
        //         422
        //     );
        // }

        $priceRange = $this->productService->getPriceByFilters($request->all());

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
        $dto = ProductFilterDTO::fromRequest($request);

        // if (!$dto->validate()) {
        //     return $this->sendRes(
        //         config('responses.validation')[app()->getLocale()],
        //         false,
        //         null,
        //         $dto->getErrors(),
        //         422
        //     );
        // }

        $tags = $this->productService->getTagsByFilters($request->all());

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
        // $dto = ProductFilterDTO::fromRequest($request);

        // if (!$dto->validate()) {
        //     return $this->sendRes(
        //         config('responses.validation')[app()->getLocale()],
        //         false,
        //         null,
        //         $dto->getErrors(),
        //         422
        //     );
        // }

        $inputs = $this->productService->getInputsByFilters($request->all());

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
        // $dto = ProductFilterDTO::fromRequest($request);

        // if (!$dto->validate()) {
        //     return $this->sendRes(
        //         config('responses.validation')[app()->getLocale()],
        //         false,
        //         null,
        //         $dto->getErrors(),
        //         422
        //     );
        // }

        $variants = $this->productService->getTreesByFilters($request->all());

        return $this->sendRes(
            config('responses.success'),
            true,
            $variants,
            [],
            200
        );
    }
}
