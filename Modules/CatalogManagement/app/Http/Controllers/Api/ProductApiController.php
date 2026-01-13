<?php

namespace Modules\CatalogManagement\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\CatalogManagement\app\DTOs\BrandFilterDTO;
use Modules\CatalogManagement\app\DTOs\ProductFilterDTO;
use Modules\CatalogManagement\app\Http\Requests\Api\FilterTypeRequest;
use Modules\CatalogManagement\app\Services\Api\ProductApiService;
use Modules\CatalogManagement\app\Http\Resources\Api\ProductResource;
use Modules\CatalogManagement\app\Http\Resources\Api\VendorProductResource;
use Modules\CatalogManagement\app\Http\Requests\Api\ProductReviewRequest;
use Modules\CatalogManagement\app\Http\Resources\Api\BrandApiResource;
use Modules\CatalogManagement\app\Http\Resources\Api\BundleCategoryResource;
use Modules\CatalogManagement\app\Http\Resources\Api\SimpleProductResource;
use Modules\CatalogManagement\app\Http\Resources\Api\VariantConfigurationKeyResource;
use Modules\CatalogManagement\app\Http\Resources\Api\BundleResource;
use Modules\CatalogManagement\app\Http\Resources\Api\ProductBySlugResource;
use Modules\CatalogManagement\app\Services\Api\BrandApiService;
use Modules\CatalogManagement\app\Services\Api\BundleApiService;
use Modules\CatalogManagement\app\Services\Api\BundleCategoryApiService;
use Modules\CatalogManagement\app\Services\OccasionService;
use Modules\CategoryManagment\app\Http\Resources\Api\GeneralResoruce;
use Modules\CategoryManagment\app\Services\CategoryService;

class ProductApiController extends Controller
{
    use Res;

    public function __construct(
        protected ProductApiService $productService,
        protected CategoryService $categoryService,
        protected BrandApiService $brandApiService,
        protected BundleCategoryApiService $bundleCategoryApiService,
        protected BundleApiService $bundleApiService,
        protected OccasionService $occasionService,
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
     * Get product by slug with all vendors, prices, and stock
     * GET /api/products/product-by-slug/{slug}
     */
    public function getProductBySlug(string $slug)
    {
        $data = $this->productService->getProductBySlug($slug);
        if (!$data) {
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
            new ProductBySlugResource($data),
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
        $filters = $request->all();
        
        // Support both main_category_id and category_id - sync them so both work the same way
        $categoryValue = $filters['main_category_id'] ?? $filters['category_id'] ?? null;
        if ($categoryValue) {
            $filters['category_id'] = $categoryValue;
            $filters['main_category_id'] = $categoryValue;
        }
        
        $filterData = $this->productService->getFilters($filters);
        
        // Handle categories - could be array or collection
        $categories = $filterData['categories'] ?? [];
        if (is_array($categories)) {
            // Already formatted as array (including empty array)
            $categoriesData = $categories;
        } elseif ($categories instanceof \Illuminate\Support\Collection || $categories instanceof \Illuminate\Database\Eloquent\Collection) {
            // Collection of models - use resource
            $categoriesData = $categories->isEmpty() ? [] : GeneralResoruce::collection($categories)->resolve();
        } else {
            $categoriesData = [];
        }
        
        // Handle brands - could be array or collection
        $brands = $filterData['brands'] ?? [];
        if (is_array($brands)) {
            // Already formatted as array (including empty array)
            $brandsData = $brands;
        } elseif ($brands instanceof \Illuminate\Support\Collection || $brands instanceof \Illuminate\Database\Eloquent\Collection) {
            // Collection of models - use resource
            $brandsData = $brands->isEmpty() ? [] : GeneralResoruce::collection($brands)->resolve();
        } else {
            $brandsData = [];
        }
        
        // Handle trees - could be array or collection
        $trees = $filterData['trees'] ?? [];
        if (is_array($trees)) {
            // Already formatted as array (including empty array)
            $treesData = $trees;
        } elseif ($trees instanceof \Illuminate\Support\Collection || $trees instanceof \Illuminate\Database\Eloquent\Collection) {
            // Collection of models - use resource
            $treesData = $trees->isEmpty() ? [] : VariantConfigurationKeyResource::collection($trees)->resolve();
        } else {
            $treesData = [];
        }
        
        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            [
                'categories' => $categoriesData,
                'brands' => $brandsData,
                'tags' => $filterData['tags'] ?? [],
                'trees' => $treesData,
                'biggest_price' => isset($filterData["price"]) ? collect($filterData['price']) : collect(),
            ],
            [],
            200
        );
    }

     /**
     * Get filters types
     * GET /api/products/filter-by-type
     */
    public function filterByType(FilterTypeRequest $request)
    {
        $returnedData = [];
        
        // Resolve brand_id from slug if slug is provided instead of ID
        $brandId = $request->brand_id;
        if ($brandId && !is_numeric($brandId)) {
            // If brand_id is not numeric, treat it as a slug
            $brand = \Modules\CatalogManagement\app\Models\Brand::where('slug', $brandId)->first();
            $brandId = $brand ? $brand->id : null;
        }
        
        if ($request->type == 'bundle') {
            // Get bundle categories filtered by brand if provided
            $bundleCategoryFilters = $request->all();
            $bundleCategories = $this->bundleCategoryApiService->getAll($bundleCategoryFilters, 0);
            
            // If brand_id is provided, filter bundle categories to only show those with products from that brand
            if (!empty($brandId)) {
                $bundleCategories = $bundleCategories->filter(function ($category) use ($brandId) {
                    // Check if this category has any bundles with products from the specified brand
                    return $category->bundles()->where('is_active', true)
                        ->where('admin_approval', 1)
                        ->whereHas('bundleProducts.vendorProductVariant.vendorProduct.product', function ($q) use ($brandId) {
                            $q->where('brand_id', $brandId);
                        })->exists();
                });
            }
            
            $bundleCategories = BundleCategoryResource::collection($bundleCategories)->resolve();
            $returnedData['bundle_categories'] = $bundleCategories;
            
            // Get trees and brands filtered by bundle products
            // Pass the resolved brand_id to the service
            $filters = $request->all();
            if ($brandId) {
                $filters['brand_id'] = $brandId;
            }
            $filterData = $this->productService->getFiltersByBundle($filters);
        } else {
            // Get occasions (not expired and active) filtered by brand if provided
            $filters = array_merge($request->all(), [
                'not_expired' => true,
                'active' => true,
            ]);
            
            // Update brand_id in filters if resolved from slug
            if ($brandId) {
                $filters['brand_id'] = $brandId;
            }
            
            $occasions = $this->occasionService->getAllOccasions($filters, 0);
            
            // If brand_id is provided, filter occasions to only show those with products from that brand
            // and filter the occasion products to only include products from that brand
            if (!empty($brandId)) {
                $occasions = $occasions->filter(function ($occasion) use ($brandId) {
                    // Check if this occasion has any products from the specified brand
                    return $occasion->occasionProducts()
                        ->whereHas('vendorProductVariant.vendorProduct.product', function ($q) use ($brandId) {
                            $q->where('brand_id', $brandId);
                        })->exists();
                });
                
                // Filter occasion products to only include products from the specified brand
                $occasions->each(function ($occasion) use ($brandId) {
                    $filteredProducts = $occasion->occasionProducts->filter(function ($occasionProduct) use ($brandId) {
                        $product = $occasionProduct->vendorProductVariant?->vendorProduct?->product;
                        return $product && $product->brand_id == $brandId;
                    });
                    $occasion->setRelation('occasionProducts', $filteredProducts);
                });
            }
            
            $returnedData['occasions'] = \Modules\CatalogManagement\app\Http\Resources\OccasionResource::collection($occasions)->resolve();
            
            // Get trees and brands filtered by occasion products
            $filterData = $this->productService->getFiltersByOccasion($filters);
        }
        
        $returnedData['trees'] = $filterData['trees'] ?? [];
        $returnedData['brands'] = $filterData['brands'] ?? [];
        
        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            $returnedData,
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

        $filters = $dto->toArray();
        
        // Filter by vendor - use request param if provided
        if ($request->filled('vendor_id')) {
            $filters['vendor_id'] = $request->vendor_id;
        }

        $products = $this->productService->getVariantsWithProduct($filters);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            SimpleProductResource::collection($products),
            [],
            200
        );
    }


}
