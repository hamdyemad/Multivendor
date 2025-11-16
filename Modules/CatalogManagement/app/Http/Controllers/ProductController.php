<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Validation\ValidationException;
use Modules\CatalogManagement\app\Models\Product;
use App\Services\LanguageService;
use Modules\AreaSettings\app\Resources\RegionResource;
use Modules\AreaSettings\app\Services\RegionService;
use Modules\CatalogManagement\app\Http\Resources\BrandResource;
use Modules\CatalogManagement\app\Http\Resources\TaxResource;
use Modules\CatalogManagement\app\Services\BrandService;
use Modules\CatalogManagement\app\Services\TaxService;
use Modules\CatalogManagement\app\Services\ProductService;
use Modules\CategoryManagment\app\Http\Resources\DepartmentResource;
use Modules\CategoryManagment\app\Services\DepartmentService;
use Modules\CategoryManagment\app\Services\CategoryService;
use Modules\CategoryManagment\app\Services\SubCategoryService;
use Modules\CatalogManagement\app\Http\Requests\Product\StoreProductRequest;
use Modules\CatalogManagement\app\Http\Requests\Product\UpdateProductRequest;
use Modules\Vendor\app\Services\VendorService;
use App\Models\UserType;
use Illuminate\Support\Facades\Auth;
use Modules\CatalogManagement\app\Actions\ProductAction;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected LanguageService $languageService,
        protected BrandService $brandService,
        protected DepartmentService $departmentService,
        protected CategoryService $categoryService,
        protected SubCategoryService $subCategoryService,
        protected RegionService $regionService,
        protected TaxService $taxService,
        protected VendorService $vendorService,
        protected ProductAction $productAction,
    ) {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $languages = $this->languageService->getAll();
        return view('catalogmanagement::product.index', compact('languages'));
    }

    /**
     * Datatable endpoint for server-side processing
     */
    public function datatable(Request $request)
    {
        try {
            // Get datatable data from action
            $result = $this->productAction->getDataTable($request->all());
            $dataPaginated = $result['dataPaginated'];
            return response()->json([
                'data' => $result['data'],
                'recordsTotal' => $result['totalRecords'],
                'recordsFiltered' => $result['filteredRecords'],
                'current_page' => $dataPaginated->currentPage(),
                'last_page' => $dataPaginated->lastPage(),
                'per_page' => $dataPaginated->perPage(),
                'total' => $dataPaginated->total(),
                'from' => $dataPaginated->firstItem(),
                'to' => $dataPaginated->lastItem()
            ]);

        } catch (\Exception $e) {
            Log::error('Product Datatable Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Error loading products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getAll();
        $brands = $this->brandService->getAllBrands([], 0);
        $brands = BrandResource::collection($brands)->resolve();
        $taxes = $this->taxService->getAllTaxes([], 0);
        $taxes = TaxResource::collection($taxes)->resolve();
        $regions = $this->regionService->getAllRegions([], 0);
        $regions = RegionResource::collection($regions)->resolve();
        $departments = [];
        // Get vendors for admin/super admin, or current vendor for vendor users
        $vendors = [];
        $currentUser = Auth::user();
        $userType = $currentUser->user_type_id;
        if (in_array($userType, [UserType::SUPER_ADMIN_TYPE, UserType::ADMIN_TYPE])) {
            // Admin/Super Admin can select any vendor
            $vendorsData = $this->vendorService->getAllVendors([], 0);
            $vendors = $vendorsData->map(function($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->getTranslation('name', app()->getLocale())
                ];
            })->toArray();
        } elseif ($userType === UserType::VENDOR_TYPE) {
            // Vendor can only create products for themselves
            $vendor = $currentUser->vendor;
            if ($vendor) {
                $vendors = [[
                    'id' => $vendor->id,
                    'name' => $vendor->getTranslation('name', app()->getLocale())
                ]];
            }
        }
        return view('catalogmanagement::product.form', compact('languages', 'departments', 'brands', 'taxes', 'regions', 'vendors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $data = $request->validated();
            $product = $this->productService->createProduct($data);

            // Check if it's an AJAX request
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('catalogmanagement::product.product_created_successfully'),
                    'redirect' => route('admin.products.index'),
                    'product' => $product
                ]);
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', __('catalogmanagement::product.product_created_successfully'));
        } catch (Exception $e) {
            Log::error("Product creation failed", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Check if it's an AJAX request
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('catalogmanagement::product.error_creating_product'),
                    'error_details' => $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('catalogmanagement::product.error_creating_product'))
                ->with('error_details', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        $languages = $this->languageService->getAll();
        $data = [
            'title' => __('catalogmanagement::product.product_details'),
            'product' => $product,
            'languages' => $languages
        ];
        return view('catalogmanagement::product.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = $this->productService->getProductById($id);
        $languages = $this->languageService->getAll();
        $brands = $this->brandService->getAllBrands([], 0);
        $brands = BrandResource::collection($brands)->resolve();
        $departments = $this->departmentService->getAllDepartments([
            'vendor_id' => $product->vendor_id
        ], 0)->map(function($department) {
            return [
                'id' => $department->id,
                'name' => $department->getTranslation('name', app()->getLocale())
            ];
        });
        $categories = $this->categoryService->getAllCategories([
            'department_id' => $product->department_id
        ], 0)->map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->getTranslation('name', app()->getLocale())
            ];
        });
        $subCategories = $this->subCategoryService->getAllSubCategories([
            'category_id' => $product->category_id
        ], 0)->map(function($subCategory) {
            return [
                'id' => $subCategory->id,
                'name' => $subCategory->getTranslation('name', app()->getLocale()),
                'category_id' => $subCategory->category_id
            ];
        });
        $taxes = $this->taxService->getAllTaxes([], 0);
        $taxes = TaxResource::collection($taxes)->resolve();
        $regions = $this->regionService->getAllRegions([], 0);
        $regions = RegionResource::collection($regions)->resolve();

        // Get vendors for admin/super admin, or current vendor for vendor users
        $vendors = [];
        $currentUser = Auth::user();
        $userType = $currentUser->user_type_id;

        if (in_array($userType, [UserType::SUPER_ADMIN_TYPE, UserType::ADMIN_TYPE])) {
            // Admin/Super Admin can select any vendor
            $vendorsData = $this->vendorService->getAllVendors([], 0);
            $vendors = $vendorsData->map(function($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->getTranslation('name', app()->getLocale())
                ];
            })->toArray();
        } elseif ($userType === UserType::VENDOR_TYPE) {
            // Vendor can only create products for themselves
            $vendor = $currentUser->vendor;
            if ($vendor) {
                $vendors = [[
                    'id' => $vendor->id,
                    'name' => $vendor->getTranslation('name', app()->getLocale())
                ]];
            }
        }

        $data = [
            'title' => __('catalogmanagement::product.edit_product'),
            'product' => $product,
            'languages' => $languages,
            'brands' => $brands,
            'departments' => $departments,
            'categories' => $categories,
            'subCategories' => $subCategories,
            'taxes' => $taxes,
            'regions' => $regions,
            'vendors' => $vendors,
        ];
        return view('catalogmanagement::product.form', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $data = $request->validated();

            // Handle deletion of additional images
            $deleteImages = $request->input('delete_images', []);
            if (!empty($deleteImages)) {
                $product = Product::find($id);
                if ($product) {
                    foreach ($deleteImages as $imageId) {
                        $image = $product->additionalImages()->find($imageId);
                        if ($image) {
                            // Delete the file from storage
                            if (file_exists(public_path($image->path))) {
                                unlink(public_path($image->path));
                            }
                            $image->delete();
                        }
                    }
                }
            }

            $this->productService->updateProduct($id, $data);

            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::product.product_updated_successfully'),
                'redirect' => route('admin.products.index')
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Product update failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::product.error_updating_product'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->productService->deleteProduct($id);

            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::product.product_deleted_successfully')
            ]);
        } catch (Exception $e) {
            Log::error('Product deletion failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::product.error_deleting_product'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
