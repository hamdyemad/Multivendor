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
use Modules\CatalogManagement\app\Http\Resources\Api\SimpleProductResource;
use Modules\CatalogManagement\app\Http\Resources\Api\VariantConfigurationKeyResource;
use Modules\CategoryManagment\app\Http\Resources\Api\GeneralResoruce;

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
     * Get hot deals
     * GET /api/products/hot-deals
     */
    public function hotDeals(Request $request)
    {
        $dto = ProductFilterDTO::fromRequest($request);

        $dto->has_discount = true;
        $dto->sort_by = 'price';
        $dto->sort_type = 'asc';
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
     * Get filters (categories, brands, variants, etc.)
     * GET /api/products/filters
     */
    public function filters(Request $request)
    {
        $filterData = $this->productService->getFilters($request->all());

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            [
                'category_info' => empty($filterData['category_info']) ? [] : GeneralResoruce::make($filterData['category_info'])->resolve(),
                'categories' => GeneralResoruce::collection($filterData['categories'])->resolve(),
                'brands' => GeneralResoruce::collection($filterData['brands'])->resolve(),
                'tags' => $filterData['tags'],
                'trees' => $filterData['trees'] ? VariantConfigurationKeyResource::collection($filterData['trees'])->resolve() : [],
                'biggest_price' => isset($filterData["price"]) ? collect($filterData['price']) : collect(),
            ],
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

        // To Do: Variant Trees per Product
        // "data": {
        //     "options": [
        //         {
        //             "key_id": 1,
        //             "key_name": "Door Bar",
        //             "options": [
        //                 {
        //                     "id": 1,
        //                     "name": "External Bar",
        //                     "color": null,
        //                     "children": [
        //                         {
        //                             "key_id": 2,
        //                             "key_name": "Door Direction",
        //                             "options": [
        //                                 {
        //                                     "id": 4,
        //                                     "name": "Right",
        //                                     "color": null
        //                                 },
        //                                 {
        //                                     "id": 6,
        //                                     "name": "Left",
        //                                     "color": null
        //                                 }
        //                             ]
        //                         }
        //                     ]
        //                 },
        //                 {
        //                     "id": 2,
        //                     "name": "Internal Bar",
        //                     "color": null,
        //                     "children": [
        //                         {
        //                             "key_id": 2,
        //                             "key_name": "Door Direction",
        //                             "options": [
        //                                 {
        //                                     "id": 3,
        //                                     "name": "Right",
        //                                     "color": null
        //                                 },
        //                                 {
        //                                     "id": 5,
        //                                     "name": "Left",
        //                                     "color": null
        //                                 }
        //                             ]
        //                         }
        //                     ]
        //                 }
        //             ]
        //         }
        //     ]
        // }

        $variants = $this->productService->getTreesByFilters($request->all());

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            $variants,
            [],
            200
        );
    }

    /**
     * Get all vendor product variants with their product details
     * Used for order creation - returns every variant with its product
     * GET /api/products/variants-all
     */
    public function variantsAll(Request $request)
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

        $products = $this->productService->getVariantsWithProduct($dto->toArray());

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            SimpleProductResource::collection($products),
            [],
            200
        );
    }
}
