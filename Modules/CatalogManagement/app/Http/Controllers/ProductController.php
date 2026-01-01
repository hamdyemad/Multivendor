<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Validation\ValidationException;
use Modules\CatalogManagement\app\Models\Product;
use Modules\CatalogManagement\app\Models\VendorProduct;
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
use Modules\CatalogManagement\app\Http\Requests\Product\UpdateStockPricingRequest;
use Modules\Vendor\app\Services\VendorService;
use App\Models\UserType;
use App\Traits\Res;
use Illuminate\Support\Facades\Auth;
use Modules\CatalogManagement\app\Actions\ProductAction;
use Modules\CatalogManagement\app\Http\Resources\BankProductResource;
use Modules\CatalogManagement\app\Http\Resources\VariantsConfigurationKeyResource;
use Modules\CatalogManagement\app\Models\Brand;
use Modules\CatalogManagement\app\Services\BankService;
use Modules\CatalogManagement\app\Services\VariantConfigurationKeyService;
use Modules\CategoryManagment\app\Http\Resources\CategoryResource;
use Modules\Vendor\app\Models\Vendor;

class ProductController extends Controller
{
    use Res;
    public function __construct(
        protected ProductService $productService,
        protected VariantConfigurationKeyService $variantConfigurationKeyService,
        protected LanguageService $languageService,
        protected BrandService $brandService,
        protected DepartmentService $departmentService,
        protected CategoryService $categoryService,
        protected SubCategoryService $subCategoryService,
        protected RegionService $regionService,
        protected TaxService $taxService,
        protected VendorService $vendorService,
        protected ProductAction $productAction,
        protected BankService $productBankService,
    ) {
        $this->middleware('can:products.index')->only(['index', 'datatable', 'pending', 'rejected', 'accepted']);
        $this->middleware('can:products.create')->only(['create', 'store', 'searchBankProducts']);
        $this->middleware('can:products.edit')->only(['edit', 'update', 'moveToBank']);
        $this->middleware('can:products.stock-management')->only(['stockManagement', 'updateStockPricing']);
        $this->middleware('can:products.delete')->only(['destroy']);
        $this->middleware('can:products.show')->only(['show']);
        $this->middleware('can:products.change-status')->only(['changeStatus']);
        $this->middleware('can:products.change-activation')->only(['changeActivation']);
        
        // Product Bank Permissions
        $this->middleware('can:products.bank')->only(['bankProducts', 'bankDatatable', 'bankView']);
        $this->middleware('can:products.bank.change-activation')->only(['changeBankActivation']);
        $this->middleware('can:products.bank.vendor-product.trash')->only(['trashVendorProduct']);
        $this->middleware('can:products.bank.vendor-product.restore')->only(['restoreVendorProduct']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Use the same logic as filtered methods but without status filter
        return $this->getFilteredProducts($request, null);
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
                'draw' => intval($request->input('draw', 1)), // Required for DataTables pagination
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
    public function create($lang, $countryCode)
    {
        $languages = $this->languageService->getAll();
        $brands = $this->brandService->getAllBrands([], 0);
        $brands = BrandResource::collection($brands)->resolve();
        $taxes = $this->taxService->getAllTaxes(0, [
            'is_active' => true
        ]);
        $taxes = TaxResource::collection($taxes)->resolve();
        // Get vendors for admin/super admin, or current vendor for vendor users
        $vendors = [];
        $currentUser = Auth::user();
        $userType = $currentUser->user_type_id;
        if (in_array($userType, UserType::adminIds())) {
            // Admin/Super Admin can select any vendor
            $vendorsData = $this->vendorService->getAllVendors([], 0);
            $vendors = $vendorsData->map(function($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->getTranslation('name', app()->getLocale()),
                    'name_en' => $vendor->getTranslation('name', 'en')
                ];
            })->toArray();
        } elseif (in_array($userType, UserType::vendorIds())) {
            // Vendor can only create products for themselves
            $vendor = $currentUser->vendor;
            if ($vendor) {
                $vendors = [[
                    'id' => $vendor->id,
                    'name' => $vendor->getTranslation('name', app()->getLocale()),
                    'name_en' => $vendor->getTranslation('name', 'en')
                ]];
            }
        }
        // Get variant keys for variant configuration
        $variantKeys = $this->variantConfigurationKeyService->getAllVariantConfigurationKeys([], 0);
        $variantKeys = VariantsConfigurationKeyResource::collection(
            $variantKeys->map(fn ($v) => $v->setAttribute('select2', true))
        )->resolve();
        
        // Determine if current user is a vendor
        $isVendorUser = in_array($userType, UserType::vendorIds());

        return view('catalogmanagement::product.create', compact('languages', 'brands', 'taxes', 'vendors', 'variantKeys', 'isVendorUser'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($lang, $countryCode, StoreProductRequest $request)
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
    public function show($lang, $countryCode, $id)
    {
        $product = $this->productService->getProductById($id);
        if(in_array(auth()->user()->user_type_id, UserType::vendorIds())) {
            $vendor = auth()->user()->vendor;
            if($product->vendor_id != $vendor->id) {
                return abort(401);
            }
        }
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
    public function edit($lang, $countryCode, $id)
    {
        $product = $this->productService->getProductById($id);
        if(in_array(auth()->user()->user_type_id, UserType::vendorIds())) {
            $vendor = auth()->user()->vendor;
            if($product->vendor_id != $vendor->id) {
                return abort(401);
            }
        }
        $languages = $this->languageService->getAll();
        $brands = $this->brandService->getAllBrands([], 0);
        $brands = BrandResource::collection($brands)->resolve();
        $taxes = $this->taxService->getAllTaxes(0, ['is_active' => true]);
        $taxes = TaxResource::collection($taxes)->resolve();
        $regions = $this->regionService->getAllRegions([], 0);
        $regions = RegionResource::collection($regions)->resolve();

        // Get vendors for admin/super admin, or current vendor for vendor users
        $vendors = [];
        $currentUser = Auth::user();
        $userType = $currentUser->user_type_id;
        if (in_array($userType, UserType::adminIds())) {
            // Admin/Super Admin can select any vendor
            $vendorsData = $this->vendorService->getAllVendors([], 0);
            $vendors = $vendorsData->map(function($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->getTranslation('name', app()->getLocale())
                ];
            })->toArray();
        } elseif (in_array($userType, UserType::vendorIds())) {
            // Vendor can only edit their own products
            $vendor = $currentUser->vendor;
            if ($vendor) {
                $vendors = [[
                    'id' => $vendor->id,
                    'name' => $vendor->getTranslation('name', app()->getLocale())
                ]];
            }
        }

        // Get variant keys for variant configuration
        $variantKeys = $this->variantConfigurationKeyService->getAllVariantConfigurationKeys([], 0);
        $variantKeys = VariantsConfigurationKeyResource::collection(
            $variantKeys->map(fn ($v) => $v->setAttribute('select2', true))
        )->resolve();

        $data = [
            'title' => __('catalogmanagement::product.edit_product'),
            'product' => $product,
            'languages' => $languages,
            'brands' => $brands,
            'taxes' => $taxes,
            'regions' => $regions,
            'vendors' => $vendors,
            'variantKeys' => $variantKeys,
        ];
        return view('catalogmanagement::product.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($lang, $countryCode, UpdateProductRequest $request, $id)
    {
        try {
            $data = $request->validated();
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
     * Change product status (approve/reject)
     */
    public function changeStatus($lang, $countryCode, Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,approved,rejected',
                'rejection_reason' => 'required_if:status,rejected|nullable|string|max:500',
                'bank_product_id' => 'nullable'
            ]);

            // Prepare data
            $data = [
                'status' => $request->status,
                'rejection_reason' => $request->rejection_reason,
            ];
            
            // Only add bank_product_id if it's present in the request (even if empty)
            if ($request->has('bank_product_id')) {
                $bankProductId = $request->bank_product_id;
                // If it's a valid ID, validate it exists
                if (!empty($bankProductId) && $bankProductId !== '') {
                    $exists = \Modules\CatalogManagement\app\Models\Product::withoutGlobalScopes()
                        ->where('id', $bankProductId)
                        ->exists();
                    if (!$exists) {
                        return response()->json([
                            'success' => false,
                            'message' => __('catalogmanagement::product.bank_product_not_found')
                        ], 422);
                    }
                }
                $data['bank_product_id'] = $bankProductId;
            }

            Log::info('Change status request', [
                'vendor_product_id' => $id,
                'data' => $data
            ]);

            $result = $this->productService->changeVendorProductStatus($id, $data);

            return response()->json([
                'success' => true,
                'message' => $result['message']
            ]);
        } catch (Exception $e) {
            Log::error('Product status change failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change product activation status (active/inactive)
     */
    public function changeActivation($lang, $countryCode, Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:1,2' // 1=active, 2=inactive
            ]);

            // Convert status: 1 = active (true), 2 = inactive (false)
            $isActive = $request->status == 1;

            $result = $this->productService->changeProductActivation($id, $isActive);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'new_status' => $isActive,
                'status_text' => $isActive ? __('common.active') : __('common.inactive')
            ]);

        } catch (Exception $e) {
            Log::error('Product activation status change failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display bank products listing
     * Bank products are queried from Product table directly (not VendorProduct)
     */
    public function bankProducts(Request $request)
    {
        $languages = $this->languageService->getAll();

        $brands = $this->brandService->getAllBrands([], 0);
        $brands = BrandResource::collection($brands)->map(function($brand) {
            return [
                'id' => $brand->id,
                'name' => $brand->name
            ];
        });

        $categories = $this->categoryService->getAllCategories([], 0);
        $categories = CategoryResource::collection($categories)->map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->name
            ];
        });

        return view('catalogmanagement::product.bank', compact('languages', 'brands', 'categories'));
    }

    /**
     * Datatable endpoint for bank products
     * Uses getBankDataTable which queries Product directly (not VendorProduct)
     * Bank products use Product.is_active for activation status
     */
    public function bankDatatable(Request $request)
    {
        try {
            // Get datatable data from bank-specific action (queries Product table directly)
            $filters = $request->all();
            $result = $this->productAction->getBankDataTable($filters);
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
            Log::error('Bank Product Datatable Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Error loading bank products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change bank product activation status (updates Product.is_active)
     */
    public function changeBankActivation($lang, $countryCode, Request $request, $id)
    {
        try {
            // Check if user is admin
            $currentUser = Auth::user();
            if (!in_array($currentUser->user_type_id, UserType::adminIds())) {
                return response()->json([
                    'success' => false,
                    'message' => __('common.unauthorized')
                ], 403);
            }

            $request->validate([
                'status' => 'required|in:1,2'
            ]);

            $product = Product::findOrFail($id);

            // Convert status: 1 = active (true), 2 = inactive (false)
            $newStatus = $request->status == 1;

            // Check if status is already set to the requested value
            if ($product->is_active == $newStatus) {
                return response()->json([
                    'success' => false,
                    'message' => __('catalogmanagement::product.activation_already_set')
                ]);
            }

            // Update the activation status on product
            $product->is_active = $newStatus;
            $product->save();

            Log::info('Bank product activation status changed', [
                'product_id' => $id,
                'new_status' => $newStatus,
                'changed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::product.activation_changed_successfully'),
                'new_status' => $newStatus,
                'status_text' => $newStatus ? __('common.active') : __('common.inactive')
            ]);

        } catch (Exception $e) {
            Log::error('Bank product activation status change failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::product.error_changing_activation')
            ], 500);
        }
    }

    /**
     * Display bank product details (main product data only)
     */
    public function bankView($lang, $countryCode, $id)
    {
        try {
            // Get the bank product directly from Product model (not VendorProduct)
            $product = Product::with(['brand', 'variants', 'category', 'subCategory', 'department', 'createdByUser', 'mainImage', 'additionalImages', 'variants.variantConfiguration.key', 'variants.variantConfiguration.parent_data'])
                ->where('type', Product::TYPE_BANK)
                ->findOrFail($id);

            $languages = $this->languageService->getAll();

            $data = [
                'title' => __('catalogmanagement::product.view_bank_product'),
                'product' => $product,
                'languages' => $languages,
            ];

            return view('catalogmanagement::product.bank-view', $data);

        } catch (Exception $e) {
            Log::error('Bank product view error: ' . $e->getMessage(), [
                'product_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return abort(404, 'Bank product not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($lang, $countryCode, $id)
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

    /**
     * Show stock and pricing management page
     */
    public function stockManagement($lang, $countryCode, $id)
    {
        $product = $this->productService->getProductById($id);
        $languages = $this->languageService->getAll();
        $regions = $this->regionService->getAllRegions([], 0);
        $regions = RegionResource::collection($regions)->resolve();

        // Get taxes for the global vendor product section
        $taxes = $this->taxService->getAllTaxes(0, ['is_active' => true]);
        $taxes = TaxResource::collection($taxes)->resolve();

        // Get variant configuration keys for the variant section
        $variantKeys = $this->variantConfigurationKeyService->getAllVariantConfigurationKeys([], 0);
        $variantKeys = VariantsConfigurationKeyResource::collection(
            $variantKeys->map(fn ($v) => $v->setAttribute('select2', true))
        )->resolve();

        return view('catalogmanagement::product.stock-management', compact(
            'product',
            'languages',
            'regions',
            'taxes',
            'variantKeys'
        ));
    }

    /**
     * Update stock and pricing only
     * Only validates Step 3: Configuration Type, Pricing, and Stock
     */
    public function updateStockPricing(UpdateStockPricingRequest $request, $lang, $countryCode, $id)
    {
        try {
            // Get validated data (only Step 3 fields)
            $data = $request->validated();

            Log::info('Stock pricing update started', [
                'product_id' => $id,
                'configuration_type' => $data['configuration_type'] ?? null,
                'has_variants' => isset($data['variants']),
                'variants_count' => isset($data['variants']) ? count($data['variants']) : 0,
            ]);

            // Update stock and pricing through service layer
            $this->productService->updateStockAndPricing($id, $data);

            Log::info('Stock pricing updated successfully', ['product_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::product.stock_pricing_updated'),
                'redirect' => route('admin.products.index')
            ]);
        } catch (ValidationException $e) {
            Log::warning('Stock pricing validation failed', [
                'product_id' => $id,
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('common.validation_error'),
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Stock pricing update failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::product.error_saving_pricing_stock'),
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display pending products
     */
    public function pending(Request $request)
    {
        // Use the same logic as index() but with status filter
        return $this->getFilteredProducts($request, 'pending');
    }

    /**
     * Display rejected products
     */
    public function rejected(Request $request)
    {
        // Use the same logic as index() but with status filter
        return $this->getFilteredProducts($request, 'rejected');
    }

    /**
     * Display accepted products
     */
    public function accepted(Request $request)
    {
        // Use the same logic as index() but with status filter
        return $this->getFilteredProducts($request, 'approved');
    }

    /**
     * Private method to get filtered products using the same pattern as index()
     */
    private function getFilteredProducts(Request $request, ?string $statusFilter)
    {
        $languages = $this->languageService->getAll();
        $departments = $this->departmentService->getAllDepartments([], 0);
        $departments = DepartmentResource::collection($departments)->resolve();
        // Get filter data for admin users - same logic as index() method
        $vendors = [];
        $brands = [];
        $categories = [];

        if (auth()->user() && in_array(auth()->user()->user_type_id, \App\Models\UserType::adminIds())) {
            $vendors = Vendor::with('translations')->get()->map(function($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->name
                ];
            });

            $brands = $this->brandService->getAllBrands([], 0);
            $brands = BrandResource::collection($brands)->map(function($brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->name
                ];
            });
        }

        $bankProducts = $this->productBankService->getAllBankProducts();
        $bankProducts = BankProductResource::collection($bankProducts)->resolve();
        return view('catalogmanagement::product.index', compact('languages', 'departments', 'vendors', 'brands', 'categories', 'statusFilter', 'bankProducts'));
    }

    /**
     * Move a product to bank (change product type to 'bank')
     */
    public function moveToBank($lang, $countryCode, Request $request, $id)
    {
        try {
            // Check if user is admin
            $currentUser = Auth::user();
            if (!in_array($currentUser->user_type_id, UserType::adminIds())) {
                return response()->json([
                    'success' => false,
                    'message' => __('common.unauthorized')
                ], 403);
            }

            $result = $this->productService->moveProductToBank($id);

            return response()->json([
                'success' => true,
                'message' => $result['message']
            ]);

        } catch (Exception $e) {
            Log::error('Move to bank failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Trash vendor product (soft delete)
     */
    public function trashVendorProduct($lang, $countryCode, $id)
    {
        try {
            // Check if user is admin
            $currentUser = Auth::user();
            if (!in_array($currentUser->user_type_id, UserType::adminIds())) {
                return response()->json([
                    'success' => false,
                    'message' => __('common.unauthorized')
                ], 403);
            }

            $vendorProduct = VendorProduct::findOrFail($id);
            $vendorProduct->delete(); // Soft delete

            Log::info('Vendor product trashed', [
                'vendor_product_id' => $id,
                'trashed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::product.vendor_product_trashed_successfully')
            ]);

        } catch (Exception $e) {
            Log::error('Trash vendor product failed', [
                'vendor_product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::product.error_trashing_vendor_product')
            ], 500);
        }
    }

    /**
     * Restore vendor product
     */
    public function restoreVendorProduct($lang, $countryCode, $id)
    {
        try {
            // Check if user is admin
            $currentUser = Auth::user();
            if (!in_array($currentUser->user_type_id, UserType::adminIds())) {
                return response()->json([
                    'success' => false,
                    'message' => __('common.unauthorized')
                ], 403);
            }

            DB::beginTransaction();

            $vendorProduct = VendorProduct::withTrashed()->findOrFail($id);
            
            // Restore the vendor product
            $vendorProduct->restore();

            // Restore variants and their stocks
            $variants = \Modules\CatalogManagement\app\Models\VendorProductVariant::withTrashed()
                ->where('vendor_product_id', $id)
                ->get();

            foreach ($variants as $variant) {
                $variant->restore();
                
                // Restore stocks for this variant
                \Modules\CatalogManagement\app\Models\VendorProductVariantStock::withTrashed()
                    ->where('vendor_product_variant_id', $variant->id)
                    ->restore();
            }

            // Also restore the product if it was soft-deleted
            if ($vendorProduct->product && $vendorProduct->product->trashed()) {
                $vendorProduct->product->restore();
            }

            DB::commit();

            Log::info('Vendor product restored with variants and stocks', [
                'vendor_product_id' => $id,
                'variants_count' => $variants->count(),
                'restored_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::product.vendor_product_restored_successfully')
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Restore vendor product failed', [
                'vendor_product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::product.error_restoring_vendor_product')
            ], 500);
        }
    }

    /**
     * Search bank products for product creation
     * This endpoint is accessible to users with products.create permission
     */
    public function searchBankProducts(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $vendorId = $request->get('vendor_id');
            $perPage = (int) $request->get('per_page', 20);

            // Don't exclude vendor's existing products - let them select any bank product
            // The exclude_vendor_id in the repository filters by vendor's departments
            $products = $this->productBankService->getAllBankProducts([
                'search' => $search,
                'vendor_id' => $vendorId, // Use vendor_id to filter by departments only
            ], $perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => \Modules\CatalogManagement\app\Http\Resources\BankProductResource::collection($products->items()),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'total' => $products->total()
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Search bank products error: ' . $e->getMessage(), [
                'search' => $request->get('search'),
                'vendor_id' => $request->get('vendor_id'),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
