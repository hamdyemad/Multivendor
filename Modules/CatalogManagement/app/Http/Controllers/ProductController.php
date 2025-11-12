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
    ) {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('catalogmanagement::product.index');
    }

    /**
     * Get products data for DataTable
     */
    public function datatable(Request $request)
    {
        $products = Product::with(['brand', 'category', 'variants'])
            ->select('products.*');

        return datatables($products)
            ->addColumn('title', function ($product) {
                return $product->getTranslation('title') ?: 'No Title';
            })
            ->addColumn('brand_name', function ($product) {
                return $product->brand ? $product->brand->getTranslation('name') : 'No Brand';
            })
            ->addColumn('category_name', function ($product) {
                return $product->category ? $product->category->getTranslation('name') : 'No Category';
            })
            ->addColumn('variants_count', function ($product) {
                return $product->variants->count();
            })
            ->addColumn('status', function ($product) {
                return $product->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('actions', function ($product) {
                return '
                    <div class="btn-group" role="group">
                        <a href="' . route('admin.products.show', $product) . '" class="btn btn-sm btn-info">
                            <i class="uil uil-eye"></i>
                        </a>
                        <a href="' . route('admin.products.edit', $product) . '" class="btn btn-sm btn-primary">
                            <i class="uil uil-edit"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger delete-product" data-id="' . $product->id . '">
                            <i class="uil uil-trash-alt"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = $this->languageService->getAll();
        $brands = $this->brandService->getAllBrands([], 0);
        $brands = BrandResource::collection($brands)->resolve();
        $departments = $this->departmentService->getAllDepartments([], 0);
        $departments = DepartmentResource::collection($departments)->resolve();
        $taxes = $this->taxService->getAllTaxes([], 0);
        $taxes = TaxResource::collection($taxes)->resolve();
        $regions = $this->regionService->getAllRegions([], 0);
        $regions = RegionResource::collection($regions)->resolve();

        return view('catalogmanagement::product.form', compact('languages', 'brands', 'departments', 'taxes', 'regions'));
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
        $departments = $this->departmentService->getAllDepartments([], 0);
        $departments = DepartmentResource::collection($departments)->resolve();
        $taxes = $this->taxService->getAllTaxes([], 0);
        $taxes = TaxResource::collection($taxes)->resolve();
        $regions = $this->regionService->getAllRegions([], 0);
        $regions = RegionResource::collection($regions)->resolve();

        $data = [
            'title' => __('catalogmanagement::product.edit_product'),
            'product' => $product,
            'languages' => $languages,
            'brands' => $brands,
            'departments' => $departments,
            'taxes' => $taxes,
            'regions' => $regions
        ];
        return view('catalogmanagement::product.form', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $this->productService->updateProduct($id, $request->validated());

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
