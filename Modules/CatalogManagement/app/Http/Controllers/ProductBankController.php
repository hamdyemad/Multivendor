<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Modules\CatalogManagement\app\Models\Product;
use Modules\CatalogManagement\app\Models\ProductVariant;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Modules\CatalogManagement\app\Models\VendorProductVariantStock;
use App\Services\LanguageService;
use Modules\AreaSettings\app\Services\RegionService;
use Modules\CatalogManagement\app\Services\BrandService;
use Modules\CatalogManagement\app\Services\TaxService;
use Modules\CategoryManagment\app\Services\DepartmentService;
use Modules\CategoryManagment\app\Services\CategoryService;
use Modules\CategoryManagment\app\Services\SubCategoryService;
use Modules\Vendor\app\Services\VendorService;
use App\Models\UserType;
use Illuminate\Support\Str;

class ProductBankController extends Controller
{
    public function __construct(
        protected LanguageService $languageService,
        protected BrandService $brandService,
        protected DepartmentService $departmentService,
        protected CategoryService $categoryService,
        protected SubCategoryService $subCategoryService,
        protected RegionService $regionService,
        protected TaxService $taxService,
        protected VendorService $vendorService,
    ) {}

    /**
     * Display bank products (Admin view)
     */
    public function index(Request $request)
    {
        $languages = $this->languageService->getAll();

        if ($request->ajax()) {
            return $this->datatable($request);
        }
        return view('catalogmanagement::product-bank.index', compact('languages'));
    }

    /**
     * Datatable for bank products
     */
    public function datatable(Request $request)
    {
        try {
            $query = Product::with([
                'brand',
                'department',
                'category',
                'subCategory',
                'vendor',
                'createdByUser',
                'variants.variantConfiguration'
            ]);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('slug', 'like', "%{$search}%")
                      ->orWhereHas('translations', function($tq) use ($search) {
                          $tq->where('lang_key', 'title')
                             ->where('lang_value', 'like', "%{$search}%");
                      });
                });
            }

            $products = $query->latest()->paginate($request->get('length', 10));

            $data = [];
            foreach ($products as $product) {
                $data[] = [
                    'id' => $product->id,
                    'title' => $product->getTranslation('title', app()->getLocale()) ??
                              $product->getTranslation('title', 'en') ??
                              $product->getTranslation('title', 'ar') ?? 'N/A',
                    'slug' => $product->slug,
                    'brand' => $product->brand?->getTranslation('name', app()->getLocale()) ?? 'N/A',
                    'department' => $product->department?->getTranslation('name', app()->getLocale()) ?? 'N/A',
                    'category' => $product->category?->getTranslation('name', app()->getLocale()) ?? 'N/A',
                    'status' => $product->status,
                    'configuration_type' => $product->configuration_type,
                    'vendor_count' => $product->vendorProducts()->count(),
                    'created_by' => $product->createdByUser?->name ?? ($product->vendor?->getTranslation('name', app()->getLocale()) ?? 'System'),
                    'created_at' => $product->created_at,
                    'actions' => view('catalogmanagement::product-bank.actions', compact('product'))->render()
                ];
            }

            return response()->json([
                'data' => $data,
                'recordsTotal' => $products->total(),
                'recordsFiltered' => $products->total(),
            ]);

        } catch (Exception $e) {
            Log::error('Product Bank Datatable Error: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading products'], 500);
        }
    }

    /**
     * Show form for creating new bank product
     */
    public function create($lang, $countryCode)
    {
        $languages = $this->languageService->getAll();
        $brands = $this->brandService->getAllBrands([], 0);
        $taxes = $this->taxService->getAllTaxes(0, []);
        $regions = $this->regionService->getAllRegions([], 0);
        $departments = $this->departmentService->getAllDepartments([], 0);

        return view('catalogmanagement::product-bank.form', compact(
            'languages', 'brands', 'taxes', 'regions', 'departments'
        ));
    }

    /**
     * Store new bank product
     */
    public function store($lang, $countryCode, Request $request)
    {
        try {
            DB::beginTransaction();

            // Create base product
            $product = Product::create([
                'slug' => $this->generateSlug($request->input('translations.1.title', 'product')),
                'is_active' => true,
                'configuration_type' => $request->configuration_type,
                'status' => Auth::user()->user_type_id === UserType::VENDOR_TYPE ? 'pending' : 'approved',
                'vendor_id' => Auth::user()->user_type_id === UserType::VENDOR_TYPE ? Auth::user()->vendor?->id : $request->vendor_id,
                'brand_id' => $request->brand_id,
                'department_id' => $request->department_id,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'created_by_user_id' => Auth::id(),
            ]);

            // Store translations
            $this->storeTranslations($product, $request->translations);

            // Handle variants if configuration_type is variants
            if ($request->configuration_type === 'variants' && $request->variants) {
                foreach ($request->variants as $variantData) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'variant_configuration_id' => $variantData['variant_id'],
                    ]);
                }
            }

            // Handle images
            $this->handleImages($product, $request);

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('catalogmanagement::product.created_successfully'),
                    'redirect' => route('admin.product-bank.index')
                ]);
            }

            return redirect()->route('admin.product-bank.index')
                           ->with('success', __('catalogmanagement::product.created_successfully'));

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Product Bank Creation Error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('catalogmanagement::product.creation_failed') . ': ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                        ->withErrors(['error' => __('catalogmanagement::product.creation_failed')]);
        }
    }

    /**
     * Show bank product details
     */
    public function show($lang, $countryCode, $id)
    {
        $product = Product::with([
            'brand', 'department', 'category', 'subCategory', 'vendor', 'createdByUser',
            'variants.variantConfiguration', 'vendorProducts.vendor', 'attachments'
        ])->findOrFail($id);


        return view('catalogmanagement::product-bank.show', compact('product'));
    }

    /**
     * Show edit form for bank product
     */
    public function edit($lang, $countryCode, $id)
    {
        $product = Product::with(['variants', 'attachments'])->findOrFail($id);

        // Check permissions
        if (Auth::user()->user_type_id === UserType::VENDOR_TYPE) {
            if (!$product->vendor || $product->vendor->user_id !== Auth::id()) {
                abort(403, 'Unauthorized');
            }
        }

        $languages = $this->languageService->getAll();
        $brands = $this->brandService->getAllBrands([], 0);
        $taxes = $this->taxService->getAllTaxes(0, []);
        $regions = $this->regionService->getAllRegions([], 0);
        $departments = $this->departmentService->getAllDepartments([], 0);

        return view('catalogmanagement::product-bank.form', compact(
            'product', 'languages', 'brands', 'taxes', 'regions', 'departments'
        ));
    }

    /**
     * Update bank product
     */
    public function update(Request $request ,$lang, $countryCode, $id)
    {
        try {
            DB::beginTransaction();

            $product = Product::findOrFail($id);

            // Check permissions
            if (Auth::user()->user_type_id === UserType::VENDOR_TYPE) {
                if (!$product->vendor || $product->vendor->user_id !== Auth::id()) {
                    abort(403, 'Unauthorized');
                }
            }

            // Update product
            $product->update([
                'configuration_type' => $request->configuration_type,
                'brand_id' => $request->brand_id,
                'department_id' => $request->department_id,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
            ]);

            // Update translations
            $this->storeTranslations($product, $request->translations);

            // Update variants
            if ($request->configuration_type === 'variants' && $request->variants) {
                // Delete existing variants
                $product->variants()->delete();

                // Create new variants
                foreach ($request->variants as $variantData) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'variant_configuration_id' => $variantData['variant_id'],
                    ]);
                }
            }

            // Handle images
            $this->handleImages($product, $request);

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('catalogmanagement::product.updated_successfully'),
                    'redirect' => route('admin.product-bank.index')
                ]);
            }

            return redirect()->route('admin.product-bank.index')
                           ->with('success', __('catalogmanagement::product.updated_successfully'));

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Product Bank Update Error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('catalogmanagement::product.update_failed') . ': ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                        ->withErrors(['error' => __('catalogmanagement::product.update_failed')]);
        }
    }

    /**
     * Delete bank product
     */
    public function destroy($lang, $countryCode, $id)
    {
        try {
            $product = Product::findOrFail($id);

            // Check permissions
            if (Auth::user()->user_type_id === UserType::VENDOR_TYPE) {
                if (!$product->vendor || $product->vendor->user_id !== Auth::id()) {
                    abort(403, 'Unauthorized');
                }
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::product.deleted_successfully')
            ]);

        } catch (Exception $e) {
            Log::error('Product Bank Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::product.delete_failed')
            ], 500);
        }
    }

    /**
     * Get product counts for menu badges
     */
    public function getCount()
    {
        try {
            $total = Product::count();
            $pending = Product::where('status', 'pending')->count();
            $approved = Product::where('status', 'approved')->count();
            $rejected = Product::where('status', 'rejected')->count();

            return response()->json([
                'success' => true,
                'total' => $total,
                'pending' => $pending,
                'approved' => $approved,
                'rejected' => $rejected
            ]);

        } catch (Exception $e) {
            Log::error('Product Count Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'total' => 0,
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0
            ]);
        }
    }

    /**
     * Approve bank product
     */
    public function approve($lang, $countryCode, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->update(['status' => 'approved', 'status_message' => null]);

            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::product.approved_successfully')
            ]);

        } catch (Exception $e) {
            Log::error('Product Approval Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::product.approval_failed')
            ], 500);
        }
    }

    /**
     * Reject bank product
     */
    public function reject(Request $request, $lang, $countryCode, $id)
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string|min:10'
            ]);

            $product = Product::findOrFail($id);
            $product->update([
                'status' => 'rejected',
                'status_message' => $request->rejection_reason
            ]);

            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::product.rejected_successfully')
            ]);

        } catch (Exception $e) {
            Log::error('Product Rejection Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::product.rejection_failed')
            ], 500);
        }
    }

    /**
     * Helper methods
     */
    private function generateSlug($title)
    {
        $slug = \Str::slug($title);
        $count = Product::where('slug', 'like', $slug . '%')->count();
        return $count > 0 ? $slug . '-' . ($count + 1) : $slug;
    }

    private function storeTranslations($product, $translations)
    {
        foreach ($translations as $langId => $fields) {
            foreach ($fields as $key => $value) {
                if ($value) {
                    $product->translations()->updateOrCreate(
                        ['lang_id' => $langId, 'lang_key' => $key],
                        ['lang_value' => $value]
                    );
                }
            }
        }
    }

    private function handleImages($product, $request)
    {
        // Handle main image
        if ($request->hasFile('main_image')) {
            // Delete existing main image
            $product->mainImage()?->delete();

            // Store new main image
            $mainImage = $request->file('main_image');
            $product->attachments()->create([
                'file_name' => $mainImage->getClientOriginalName(),
                'file_path' => $mainImage->store('products/main', 'public'),
                'file_size' => $mainImage->getSize(),
                'mime_type' => $mainImage->getMimeType(),
                'type' => 'main_image'
            ]);
        }

        // Handle additional images
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $image) {
                $product->attachments()->create([
                    'file_name' => $image->getClientOriginalName(),
                    'file_path' => $image->store('products/additional', 'public'),
                    'file_size' => $image->getSize(),
                    'mime_type' => $image->getMimeType(),
                    'type' => 'additional_image'
                ]);
            }
        }
    }
}
