<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\CatalogManagement\app\Services\BankService;
use Modules\CatalogManagement\app\Http\Resources\BankProductResource;
use Modules\CatalogManagement\app\Http\Requests\SaveBankStockRequest;
use App\Services\LanguageService;
use Modules\Vendor\app\Services\VendorService;
use Modules\CatalogManagement\app\Services\TaxService;
use App\Models\UserType;
use App\Traits\Res;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    use Res;
    public function __construct(
        protected BankService $bankService,
        protected LanguageService $languageService,
        protected VendorService $vendorService,
        protected TaxService $taxService
    ) {}

    /**
     * Show the bank stock management page
     */
    public function stockManagement()
    {
        $languages = $this->languageService->getAll();
        $currentUser = Auth::user();
        $isVendorUser = in_array($currentUser->user_type_id, UserType::vendorIds());

        // Get vendors for admin users, or current vendor for vendor users
        if ($isVendorUser) {
            $vendors = collect([$currentUser->vendor])->filter();
        } else {
            $vendors = $this->vendorService->getAllVendors([], 0);
        }

        $taxes = $this->taxService->getAllTaxes(0, []);

        return view('catalogmanagement::product.bank-stock-management', compact(
            'languages',
            'vendors',
            'taxes',
            'isVendorUser'
        ));
    }

    /**
     * Unified API endpoint for getting bank products with various filters
     * Handles: search, vendor products, products not in vendor, etc.
     */
    public function getProducts(Request $request)
    {
        try {
            $type = $request->get('type', 'search'); // search, vendor_product, not_in_vendor
            $search = $request->get('search', '');
            $vendorId = $request->get('vendor_id');
            $productId = $request->get('product_id');
            $perPage = (int) $request->get('per_page', 20);

            switch ($type) {
                case 'vendor_product':
                    // Get specific vendor product with variants
                    if (!$productId || !$vendorId) {
                        return response()->json([
                            'success' => false,
                            'message' => 'product_id and vendor_id are required'
                        ], 400);
                    }

                    $vendorProduct = $this->bankService->getVendorProductByProductAndVendor((int) $productId, (int) $vendorId);

                    return response()->json([
                        'success' => true,
                        'data' => [
                            'product' => $vendorProduct['product'] ?? null,
                            'variants' => $vendorProduct['variants'] ?? []
                        ]
                    ]);

                case 'not_in_vendor':
                    // Get products not in vendor's catalog
                    if (!$vendorId) {
                        return response()->json([
                            'success' => false,
                            'message' => 'vendor_id is required'
                        ], 400);
                    }

                    $products = $this->bankService->getProductsNotInVendor((int) $vendorId, $search);

                    return response()->json([
                        'success' => true,
                        'data' => ['products' => $products]
                    ]);

                case 'search':
                default:
                    // Search bank products (with optional vendor exclusion)
                    $products = $this->bankService->getAllBankProducts([
                        'search' => $search,
                        'exclude_vendor_id' => $vendorId,
                    ], $perPage);
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'products' => BankProductResource::collection($products->items()),
                            'current_page' => $products->currentPage(),
                            'last_page' => $products->lastPage(),
                            'total' => $products->total()
                        ]
                    ]);
            }
        } catch (Exception $e) {
            Log::error('Bank products API error: ' . $e->getMessage(), [
                'type' => $request->get('type'),
                'vendor_id' => $vendorId,
                'product_id' => $productId,
                'search' => $search
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get taxes for dropdowns
     */
    public function getTaxes(Request $request)
    {
        try {
            $taxes = $this->taxService->getAllTaxes(0, []);

            return response()->json([
                'success' => true,
                'data' => $taxes->map(function($tax) {
                    return [
                        'id' => $tax->id,
                        'name' => $tax->name,
                    ];
                })
            ]);
        } catch (Exception $e) {
            Log::error('Get taxes error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Save bank product stock
     */
    public function saveStock(SaveBankStockRequest $request)
    {
        try {
            $data = $request->validated();

            // Log the incoming data for debugging
            Log::info('Bank stock save request:', $data);

            $result = $this->bankService->saveBankStock($data);

            return response()->json([
                'success' => true,
                'message' => 'Stock saved successfully!',
                'data' => $result
            ]);
        } catch (Exception $e) {
            Log::error('Save bank stock error: ' . $e->getMessage(), [
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error saving stock: ' . $e->getMessage()
            ], 500);
        }
    }
}
