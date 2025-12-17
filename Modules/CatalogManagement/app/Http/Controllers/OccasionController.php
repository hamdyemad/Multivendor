<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CatalogManagement\app\Http\Requests\OccasionRequest;
use Modules\CatalogManagement\app\Services\OccasionService;
use Modules\CatalogManagement\app\Interfaces\OccasionRepositoryInterface;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class OccasionController extends Controller
{
    public function __construct(
        protected OccasionService $occasionService,
        protected OccasionRepositoryInterface $occasionRepository,
        protected LanguageService $languageService
    ) {}

    /**
     * Display a listing of occasions
     */
    public function index()
    {
        $languages = $this->languageService->getAll();
        $vendors = \Modules\Vendor\app\Models\Vendor::all();
        $data = [
            'title' => trans('catalogmanagement::occasion.occasions_management'),
            'languages' => $languages,
            'vendors' => $vendors,
        ];
        return view('catalogmanagement::occasions.index', $data);
    }

    /**
     * Datatable endpoint for server-side processing
     */
    public function datatable(Request $request)
    {
        try {
            // Get filters from request
            $filters = [
                'search' => $request->get('search')['value'] ?? $request->get('search'),
                'active' => $request->get('active'),
                'created_from' => $request->get('created_from'),
                'created_until' => $request->get('created_until'),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
            ];

            // Get occasions query with filters (use 0 for no pagination in DataTables)
            $occasions = $this->occasionService->getAllOccasions($filters, 0);

            return DataTables::of($occasions)
                ->addColumn('occasion_information', function ($occasion) {
                    // Get EN and AR names
                    $nameEn = $occasion->getTranslation('name', 'en') ?? '-';
                    $nameAr = $occasion->getTranslation('name', 'ar') ?? '-';

                    // Get vendor name
                    $vendorName = $occasion->vendor->name ?? '-';

                    // Get image
                    $imageAttachment = $occasion->attachments()->where('type', 'image')->first();
                    $image = $imageAttachment ? asset('storage/' . $imageAttachment->path) : null;

                    return [
                        'name_en' => $nameEn,
                        'name_ar' => $nameAr,
                        'vendor' => $vendorName,
                        'image' => $image
                    ];
                })
                ->addColumn('start_date', function ($occasion) {
                    return $occasion->start_date ? $occasion->start_date->format('Y-m-d') : '-';
                })
                ->addColumn('end_date', function ($occasion) {
                    return $occasion->end_date ? $occasion->end_date->format('Y-m-d') : '-';
                })
                ->addColumn('is_active', function ($occasion) {
                    return $occasion->is_active;
                })
                ->addColumn('created_at', function ($occasion) {
                    return $occasion->created_at;
                })
                ->rawColumns(['occasion_information'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('catalogmanagement::occasion.error_loading_data'),
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new occasion
     */
    public function create()
    {
        $languages = $this->languageService->getAll();
        $vendors = \Modules\Vendor\app\Models\Vendor::all();

        $data = [
            'title' => trans('catalogmanagement::occasion.add_occasion'),
            'languages' => $languages,
            'vendors' => $vendors,
        ];
        return view('catalogmanagement::occasions.form', $data);
    }

    /**
     * Store a newly created occasion
     */
    public function store($lang, $countryCode, OccasionRequest $request)
    {
        \Log::info($request->all());
        try {
            $validated = $request->validated();
            $occasion = $this->occasionService->createOccasion($validated);

            // Get the count of products added to the occasion
            $productsCount = $occasion->occasionProducts()->count();
            $successMessage = trans('catalogmanagement::occasion.occasion_created') .
                             ($productsCount > 0 ? ' (' . $productsCount . ' ' . trans('catalogmanagement::occasion.products_added') . ')' : '');

            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $occasion,
                    'products_count' => $productsCount,
                    'redirect' => route('admin.occasions.index')
                ]);
            }

            return redirect()->route('admin.occasions.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('catalogmanagement::occasion.error_creating_occasion') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', trans('catalogmanagement::occasion.error_creating_occasion') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified occasion
     */
    public function show($lang, $countryCode, $id)
    {
        $occasion = $this->occasionService->getOccasionById($id);
        $languages = $this->languageService->getAll();

        $data = [
            'title' => trans('catalogmanagement::occasion.view_occasion'),
            'occasion' => $occasion,
            'languages' => $languages,
        ];
        return view('catalogmanagement::occasions.show', $data);
    }

    /**
     * Show the form for editing the specified occasion
     */
    public function edit($lang, $countryCode, $id)
    {
        $occasion = $this->occasionService->getOccasionById($id);
        $languages = $this->languageService->getAll();
        $vendors = \Modules\Vendor\app\Models\Vendor::all();
        $data = [
            'title' => trans('catalogmanagement::occasion.edit_occasion'),
            'occasion' => $occasion,
            'languages' => $languages,
            'vendors' => $vendors,
        ];
        return view('catalogmanagement::occasions.form', $data);
    }

    /**
     * Update the specified occasion
     */
    public function update($lang, $countryCode, OccasionRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $occasion = $this->occasionService->updateOccasion($id, $validated);

            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('catalogmanagement::occasion.occasion_updated'),
                    'data' => $occasion,
                    'redirect' => route('admin.occasions.index')
                ]);
            }

            return redirect()->route('admin.occasions.index')
                ->with('success', trans('catalogmanagement::occasion.occasion_updated'));
        } catch (\Exception $e) {
            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('catalogmanagement::occasion.error_updating_occasion') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', trans('catalogmanagement::occasion.error_updating_occasion') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified occasion
     */
    public function destroy($lang, $countryCode, Request $request, $id)
    {
        try {
            $this->occasionService->deleteOccasion($id);

            return response()->json([
                'status' => true,
                'message' => trans('catalogmanagement::occasion.occasion_deleted'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => trans('catalogmanagement::occasion.error_deleting_occasion') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    // /**
    //  * Search products for Select2 AJAX
    //  */
    // public function searchProducts(Request $request)
    // {
    //     try {
    //         $vendorId = $request->input('vendor_id');
    //         $search = $request->input('search', '');
    //         $page = $request->input('page', 1);
    //         $perPage = 20;

    //         if (!$vendorId) {
    //             return response()->json([
    //                 'results' => [],
    //                 'pagination' => ['more' => false]
    //             ]);
    //         }

    //         // Query vendor product variants
    //         $query = \Modules\Vendor\app\Models\VendorProductVariant::query()
    //             ->where('vendor_id', $vendorId)
    //             ->with(['product', 'variantConfiguration'])
    //             ->when($search, function ($q) use ($search) {
    //                 $q->whereHas('product', function ($query) use ($search) {
    //                     $query->where('name', 'like', "%{$search}%")
    //                           ->orWhere('sku', 'like', "%{$search}%");
    //                 });
    //             });

    //         $total = $query->count();
    //         $variants = $query->skip(($page - 1) * $perPage)
    //                          ->take($perPage)
    //                          ->get();

    //         $results = $variants->map(function ($variant) {
    //             return [
    //                 'id' => $variant->id,
    //                 'text' => $variant->product->name . ' - ' . ($variant->variantConfiguration->name ?? 'Default'),
    //                 'product_name' => $variant->product->name ?? 'N/A',
    //                 'variant_name' => $variant->variantConfiguration->name ?? 'Default',
    //                 'sku' => $variant->sku ?? 'N/A',
    //                 'original_price' => number_format($variant->price ?? 0, 2),
    //                 'special_price' => $variant->special_price ? number_format($variant->special_price, 2) : null,
    //                 'image' => $variant->product->main_image ? asset('storage/' . $variant->product->main_image) : null,
    //             ];
    //         });

    //         return response()->json([
    //             'results' => $results,
    //             'pagination' => [
    //                 'more' => ($page * $perPage) < $total
    //             ]
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'results' => [],
    //             'pagination' => ['more' => false],
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Toggle occasion status
     */
    public function toggleStatus($lang, $countryCode, Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'is_active' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $occasion = $this->occasionService->toggleOccasionStatus($id);

            return response()->json([
                'status' => true,
                'message' => trans('catalogmanagement::occasion.status_changed_successfully'),
                'data' => $occasion
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => trans('catalogmanagement::occasion.error_changing_status') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove a product from occasion
     */
    public function destroyProduct(Request $request, $lang, $countryCode, $occasion, $product)
    {
        try {
            // Find and delete the occasion product
            $occasionProduct = \Modules\CatalogManagement\app\Models\OccasionProduct::where('occasion_id', $occasion)
                ->where('id', $product)
                ->firstOrFail();

            $occasionProduct->delete();

            return response()->json([
                'status' => true,
                'message' => trans('catalogmanagement::occasion.product_deleted_successfully'),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => trans('catalogmanagement::occasion.error_deleting_product'),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => trans('catalogmanagement::occasion.error_deleting_product') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update product positions in occasion
     */
    public function updatePositions($lang, $countryCode, Request $request, $occasion)
    {
        try {
            $positions = $request->input('positions', []);

            if (empty($positions)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('common.no_positions_provided'),
                ], 422);
            }

            // Update positions for each product
            foreach ($positions as $item) {
                if (!isset($item['product_id']) || !isset($item['position'])) {
                    continue;
                }

                \Modules\CatalogManagement\app\Models\OccasionProduct::where('id', $item['product_id'])
                    ->where('occasion_id', $occasion)
                    ->update(['position' => $item['position']]);
            }

            return response()->json([
                'status' => true,
                'message' => trans('common.order_updated_successfully') ?? 'Order updated successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating occasion product positions: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => trans('common.error_updating_order') ?? 'Error updating order',
            ], 500);
        }
    }

    /**
     * Update special price for occasion product
     */
    public function updateSpecialPrice(Request $request, $lang, $countryCode, $occasion, $product)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'special_price' => 'nullable|numeric|min:0',
            ]);

            // Find and update the occasion product
            $occasionProduct = \Modules\CatalogManagement\app\Models\OccasionProduct::where('occasion_id', $occasion)
                ->where('id', $product)
                ->firstOrFail();

            $occasionProduct->update([
                'special_price' => $validated['special_price'] ?? null,
            ]);

            return response()->json([
                'status' => true,
                'message' => trans('catalogmanagement::occasion.special_price') . ' ' . trans('common.updated'),
                'data' => $occasionProduct,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => trans('catalogmanagement::occasion.error_deleting_product'),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => trans('common.error') . ': ' . $e->getMessage(),
            ], 500);
        }
    }
}
