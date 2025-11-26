<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Modules\CatalogManagement\app\Models\Product;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Modules\CatalogManagement\app\Models\VendorProductVariantStock;
use App\Services\LanguageService;
use Modules\AreaSettings\app\Services\RegionService;
use Modules\CatalogManagement\app\Services\TaxService;
use Modules\CategoryManagment\app\Services\DepartmentService;
use Modules\CategoryManagment\app\Services\CategoryService;

class VendorProductController extends Controller
{
    public function __construct(
        protected LanguageService $languageService,
        protected RegionService $regionService,
        protected TaxService $taxService,
        protected DepartmentService $departmentService,
        protected CategoryService $categoryService,
    ) {}

    /**
     * Display available bank products for vendor to import
     */
    public function availableProducts(Request $request)
    {
        $languages = $this->languageService->getAll();
        $departments = $this->departmentService->getAllDepartments([], 0);
        $categories = $this->categoryService->getAllCategories([], 0);

        if ($request->ajax()) {
            return $this->availableProductsDatatable($request);
        }

        return view('catalogmanagement::vendor-products.available', compact('languages', 'departments', 'categories'));
    }

    /**
     * Datatable for available bank products
     */
    public function availableProductsDatatable(Request $request)
    {
        try {
            $vendor = Auth::user()->vendor;

            $query = Product::with([
                'brand', 'department', 'category', 'subCategory', 'variants.variantConfiguration'
            ])
            ->where('status', 'approved')
            ->whereDoesntHave('vendorProducts', function($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            });

            // Apply filters
            if ($request->filled('department_id')) {
                $query->where('department_id', $request->department_id);
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
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
                    'brand' => $product->brand?->getTranslation('name', app()->getLocale()) ?? 'N/A',
                    'department' => $product->department?->getTranslation('name', app()->getLocale()) ?? 'N/A',
                    'category' => $product->category?->getTranslation('name', app()->getLocale()) ?? 'N/A',
                    'configuration_type' => $product->configuration_type,
                    'variants_count' => $product->variants()->count(),
                    'created_at' => $product->created_at,
                    'actions' => view('catalogmanagement::vendor-products.available-actions', compact('product'))->render()
                ];
            }

            return response()->json([
                'data' => $data,
                'recordsTotal' => $products->total(),
                'recordsFiltered' => $products->total(),
            ]);

        } catch (Exception $e) {
            Log::error('Available Products Datatable Error: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading products'], 500);
        }
    }

    /**
     * Show form to import/add product to vendor store
     */
    public function importForm($productId)
    {
        $product = Product::with(['variants.variantConfiguration', 'brand', 'department', 'category'])
                         ->where('status', 'approved')
                         ->findOrFail($productId);

        $vendor = Auth::user()->vendor;

        // Check if already imported
        if ($product->vendorProducts()->where('vendor_id', $vendor->id)->exists()) {
            return redirect()->route('vendor.available-products.index')
                           ->with('error', __('catalogmanagement::product.already_imported'));
        }

        $languages = $this->languageService->getAll();
        $taxes = $this->taxService->getAllTaxes([], 0);
        $regions = $this->regionService->getAllRegions([], 0);

        return view('catalogmanagement::vendor-products.import-form', compact(
            'product', 'languages', 'taxes', 'regions'
        ));
    }

    /**
     * Import product to vendor store
     */
    public function import(Request $request, $productId)
    {
        try {
            DB::beginTransaction();

            $product = Product::with('variants')->findOrFail($productId);
            $vendor = Auth::user()->vendor;

            // Check if already imported
            if ($product->vendorProducts()->where('vendor_id', $vendor->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => __('catalogmanagement::product.already_imported')
                ], 400);
            }

            // Create vendor product
            $vendorProduct = VendorProduct::create([
                'vendor_id' => $vendor->id,
                'product_id' => $product->id,
                'tax_id' => $request->tax_id,
                'sku' => $request->sku,
                'points' => $request->points ?? 0,
                'max_per_order' => $request->max_per_order ?? 1,
                'offer_date_view' => $request->boolean('offer_date_view'),
                'is_active' => $request->boolean('is_active', true),
                'is_featured' => $request->boolean('is_featured', false),
            ]);

            // Handle variants and pricing
            if ($product->configuration_type === 'variants' && $request->variants) {
                foreach ($request->variants as $variantId => $variantData) {
                    $productVariant = $product->variants()->where('variant_configuration_id', $variantId)->first();

                    if ($productVariant) {
                        $vendorVariant = VendorProductVariant::create([
                            'vendor_product_id' => $vendorProduct->id,
                            'variant_configuration_id' => $variantId,
                            'sku' => $variantData['sku'],
                            'price' => $variantData['price'],
                            'has_offer' => $variantData['has_offer'] ?? false,
                            'price_before_discount' => $variantData['price_before_discount'] ?? null,
                            'offer_end_date' => $variantData['offer_end_date'] ?? null,
                        ]);

                        // Handle stock for each region
                        if (isset($variantData['stocks'])) {
                            foreach ($variantData['stocks'] as $regionId => $quantity) {
                                VendorProductVariantStock::create([
                                    'vendor_product_variant_id' => $vendorVariant->id,
                                    'region_id' => $regionId,
                                    'quantity' => $quantity ?? 0,
                                ]);
                            }
                        }
                    }
                }
            } else {
                // Simple product - create single variant
                $vendorVariant = VendorProductVariant::create([
                    'vendor_product_id' => $vendorProduct->id,
                    'variant_configuration_id' => null, // Simple product
                    'sku' => $request->sku,
                    'price' => $request->price,
                    'has_offer' => $request->boolean('has_offer'),
                    'price_before_discount' => $request->price_before_discount,
                    'offer_end_date' => $request->offer_end_date,
                ]);

                // Handle stock for simple product
                if ($request->stocks) {
                    foreach ($request->stocks as $regionId => $quantity) {
                        VendorProductVariantStock::create([
                            'vendor_product_variant_id' => $vendorVariant->id,
                            'region_id' => $regionId,
                            'quantity' => $quantity ?? 0,
                        ]);
                    }
                }
            }

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('catalogmanagement::product.imported_successfully'),
                    'redirect' => route('vendor.my-products.index')
                ]);
            }

            return redirect()->route('vendor.my-products.index')
                           ->with('success', __('catalogmanagement::product.imported_successfully'));

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Product Import Error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('catalogmanagement::product.import_failed') . ': ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                        ->withErrors(['error' => __('catalogmanagement::product.import_failed')]);
        }
    }

    /**
     * Display vendor's products
     */
    public function myProducts(Request $request)
    {
        $languages = $this->languageService->getAll();

        if ($request->ajax()) {
            return $this->myProductsDatatable($request);
        }

        return view('catalogmanagement::vendor-products.my-products', compact('languages'));
    }

    /**
     * Datatable for vendor's products
     */
    public function myProductsDatatable(Request $request)
    {
        try {
            $vendor = Auth::user()->vendor;

            $query = VendorProduct::with([
                'product.brand', 'product.department', 'product.category',
                'tax', 'variants.variantConfiguration'
            ])->where('vendor_id', $vendor->id);

            // Apply filters
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('sku', 'like', "%{$search}%")
                      ->orWhereHas('product.translations', function($tq) use ($search) {
                          $tq->where('lang_key', 'title')
                             ->where('lang_value', 'like', "%{$search}%");
                      });
                });
            }

            $vendorProducts = $query->latest()->paginate($request->get('length', 10));

            $data = [];
            foreach ($vendorProducts as $vendorProduct) {
                $product = $vendorProduct->product;
                $totalStock = $vendorProduct->variants->sum(function($variant) {
                    return $variant->getTotalStock();
                });

                $data[] = [
                    'id' => $vendorProduct->id,
                    'title' => $product->getTranslation('title', app()->getLocale()) ??
                              $product->getTranslation('title', 'en') ??
                              $product->getTranslation('title', 'ar') ?? 'N/A',
                    'sku' => $vendorProduct->sku,
                    'brand' => $product->brand?->getTranslation('name', app()->getLocale()) ?? 'N/A',
                    'tax' => $vendorProduct->tax?->getTranslation('name', app()->getLocale()) ?? 'N/A',
                    'variants_count' => $vendorProduct->variants()->count(),
                    'total_stock' => $totalStock,
                    'is_active' => $vendorProduct->is_active,
                    'is_featured' => $vendorProduct->is_featured,
                    'created_at' => $vendorProduct->created_at,
                    'actions' => view('catalogmanagement::vendor-products.my-products-actions', compact('vendorProduct'))->render()
                ];
            }

            return response()->json([
                'data' => $data,
                'recordsTotal' => $vendorProducts->total(),
                'recordsFiltered' => $vendorProducts->total(),
            ]);

        } catch (Exception $e) {
            Log::error('My Products Datatable Error: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading products'], 500);
        }
    }

    /**
     * Show vendor product details
     */
    public function show($id)
    {
        $vendor = Auth::user()->vendor;
        $vendorProduct = VendorProduct::with([
            'product.brand', 'product.department', 'product.category', 'product.subCategory',
            'tax', 'variants.variantConfiguration', 'variants.stocks.region'
        ])->where('vendor_id', $vendor->id)->findOrFail($id);

        return view('catalogmanagement::vendor-products.show', compact('vendorProduct'));
    }

    /**
     * Show stock management form
     */
    public function manageStock($id)
    {
        $vendor = Auth::user()->vendor;
        $vendorProduct = VendorProduct::with([
            'product', 'variants.variantConfiguration', 'variants.stocks.region'
        ])->where('vendor_id', $vendor->id)->findOrFail($id);

        $regions = $this->regionService->getAllRegions([], 0);

        return view('catalogmanagement::vendor-products.manage-stock', compact('vendorProduct', 'regions'));
    }

    /**
     * Update stock quantities
     */
    public function updateStock(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $vendor = Auth::user()->vendor;
            $vendorProduct = VendorProduct::where('vendor_id', $vendor->id)->findOrFail($id);

            if ($request->variants) {
                foreach ($request->variants as $variantId => $variantData) {
                    $vendorVariant = $vendorProduct->variants()->find($variantId);

                    if ($vendorVariant && isset($variantData['stocks'])) {
                        foreach ($variantData['stocks'] as $regionId => $quantity) {
                            VendorProductVariantStock::updateOrCreate(
                                [
                                    'vendor_product_variant_id' => $vendorVariant->id,
                                    'region_id' => $regionId
                                ],
                                ['quantity' => $quantity ?? 0]
                            );
                        }
                    }
                }
            }

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('catalogmanagement::product.stock_updated_successfully')
                ]);
            }

            return redirect()->route('vendor.my-products.index')
                           ->with('success', __('catalogmanagement::product.stock_updated_successfully'));

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Stock Update Error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('catalogmanagement::product.stock_update_failed')
                ], 500);
            }

            return back()->withErrors(['error' => __('catalogmanagement::product.stock_update_failed')]);
        }
    }

    /**
     * Update vendor product settings (active, featured, etc.)
     */
    public function updateSettings(Request $request, $id)
    {
        try {
            $vendor = Auth::user()->vendor;
            $vendorProduct = VendorProduct::where('vendor_id', $vendor->id)->findOrFail($id);

            $vendorProduct->update([
                'is_active' => $request->boolean('is_active'),
                'is_featured' => $request->boolean('is_featured'),
                'points' => $request->points ?? $vendorProduct->points,
                'max_per_order' => $request->max_per_order ?? $vendorProduct->max_per_order,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::product.settings_updated_successfully')
            ]);

        } catch (Exception $e) {
            Log::error('Product Settings Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::product.settings_update_failed')
            ], 500);
        }
    }

    /**
     * Remove product from vendor store
     */
    public function remove($id)
    {
        try {
            $vendor = Auth::user()->vendor;
            $vendorProduct = VendorProduct::where('vendor_id', $vendor->id)->findOrFail($id);

            $vendorProduct->delete();

            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::product.removed_successfully')
            ]);

        } catch (Exception $e) {
            Log::error('Product Remove Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::product.remove_failed')
            ], 500);
        }
    }

    /**
     * Get available products count for menu badge
     */
    public function getAvailableCount()
    {
        try {
            $vendor = Auth::user()->vendor;

            $available = Product::where('status', 'approved')
                              ->whereDoesntHave('vendorProducts', function($q) use ($vendor) {
                                  $q->where('vendor_id', $vendor->id);
                              })
                              ->count();

            return response()->json([
                'success' => true,
                'available' => $available
            ]);

        } catch (Exception $e) {
            Log::error('Available Products Count Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'available' => 0
            ]);
        }
    }

    /**
     * Get vendor's products count for menu badge
     */
    public function getMyProductsCount()
    {
        try {
            $vendor = Auth::user()->vendor;
            $total = VendorProduct::where('vendor_id', $vendor->id)->count();
            $active = VendorProduct::where('vendor_id', $vendor->id)->where('is_active', true)->count();

            return response()->json([
                'success' => true,
                'total' => $total,
                'active' => $active
            ]);

        } catch (Exception $e) {
            Log::error('My Products Count Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'total' => 0,
                'active' => 0
            ]);
        }
    }
}
