<?php

namespace Modules\Order\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Order\app\Services\OrderService;
use Modules\Order\app\Http\Requests\CreateOrderRequest;
use Modules\Order\app\Http\Requests\StoreOrderRequest;
use Modules\Order\app\Http\Requests\UpdateOrderRequest;
use Modules\Order\app\Http\Requests\ChangeOrderStageRequest;
use Modules\Order\app\Http\Requests\AddProductToOrderRequest;
use Modules\Order\app\Http\Requests\AddExtraFeeDiscountRequest;
use Modules\Order\app\Http\Requests\CreateFulfillmentRequest;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\RequestQuotation;
use App\Models\Language;
use Modules\Order\app\Http\Resources\Api\OrderStageResource;
use Modules\Order\app\Models\OrderProduct;
use Modules\Order\app\Models\OrderStage;
use Modules\Order\app\Services\OrderStageService;
use Modules\Vendor\app\Services\VendorService;
use Modules\Customer\app\Models\Customer;
use Modules\SystemSetting\app\Services\FirebaseService;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(
        OrderService $orderService,
        protected VendorService $vendorService,
        protected OrderStageService $orderStageService
    )
    {
        $this->orderService = $orderService;
        $this->middleware('can:orders.index')->only(['index', 'datatable']);
        $this->middleware('can:orders.create')->only(['create', 'store']);
        $this->middleware('can:orders.show')->only(['show']);
        $this->middleware('can:orders.edit')->only(['edit', 'update', 'changeStage']);
    }

    /**
     * Check if vendor can edit/delete the order
     * Returns true if admin or if order belongs exclusively to the vendor
     */
    private function canVendorModifyOrder(Order $order): bool
    {
        // Admin can always modify
        if (isAdmin()) {
            return true;
        }

        $currentVendorId = auth()->user()->vendor?->id;
        if (!$currentVendorId) {
            return false;
        }

        // Check if all products in the order belong to the current vendor
        $orderVendorIds = $order->products->pluck('vendor_id')->unique()->toArray();
        
        return count($orderVendorIds) === 1 && in_array($currentVendorId, $orderVendorIds);
    }

    /**
     * Get current country ID from session
     */
    private function getCurrentCountryId(): ?int
    {
        $countryCode = session('country_code');
        if (!$countryCode) {
            return null;
        }
        
        return \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orderStages = $this->orderStageService->getOrderStagesQuery()->get();
        $orderStages = OrderStageResource::collection($orderStages)->resolve();
        $languages = Language::all();
        
        // Get current country ID for filtering
        $countryId = $this->getCurrentCountryId();
        
        // Get all statistics from a single function
        $vendorId = isAdmin() ? null : (auth()->user()->vendor?->id ?? null);
        $statistics = $this->getOrderStatistics($vendorId, $countryId);
        
        $vendors = $this->vendorService->getAllVendors([]);
        $data = [
            'languages' => $languages,
            'orders_count' => $statistics['orders_count'],
            'total_price' => $statistics['total_price'],
            'vendorOrderStats' => $statistics['vendor_stats'],
            'vendors' => $vendors,
            'orderStages' => $orderStages,
        ];
        return view('order::orders.index', $data);
    }

    /**
     * DataTable endpoint for orders
     */
    public function datatable(Request $request)
    {
        try {
            // Get pagination parameters from DataTables
            $perPage = isset($request->per_page) && $request->per_page > 0 ? (int)$request->per_page : 10;
            $start = isset($request->start) && $request->start >= 0 ? (int)$request->start : 0;
            // Calculate page number from start offset
            $page = $perPage > 0 ? floor($start / $perPage) + 1 : 1;

            // Get filter parameters
            $filters = [
                'search' => $request->search ?? null,
                'created_date_from' => $request->created_date_from ?? null,
                'created_date_to' => $request->created_date_to ?? null,
                'stage_id' => $request->stage ?? null,
                'vendor_id' => $request->vendor ?? null,
                'payment_type' => $request->payment_type ?? null,
                'payment_visa_status' => $request->payment_visa_status ?? null,
            ];

            // Get total records count
            $totalRecordsQuery = $this->orderService->getAllOrders([]);
            $totalRecords = $totalRecordsQuery->count();

            // Get filtered records count
            $filteredQuery = $this->orderService->getAllOrders($filters);
            $filteredRecords = $filteredQuery->count();

            // Get orders with pagination
            $ordersQuery = $this->orderService->getAllOrders($filters);
            $orders = $ordersQuery->paginate($perPage, ['*'], 'page', $page);

            // Return raw data - rendering will be handled by DataTables in the view
            $data = [];
            $index = $start + 1; // Start index from the correct offset
            $currentVendorId = !isAdmin() ? (auth()->user()->vendorByUser?->id ?? auth()->user()->vendorById?->id ?? null) : null;
            $isVendorUser = !isAdmin();
            
            foreach ($orders as $order) {
                // Count items in this order
                $itemsCount = $order->products ? $order->products->sum('quantity') : 0;

                $vendors = $order->products->map(function ($orderProduct) {
                    return $orderProduct->vendorProduct->vendor ?? null;
                })->filter()->unique('id');

                // Get vendors with their stages from vendor_order_stages
                $vendorsWithStages = $vendors->map(function ($vendor) use ($order) {
                    $vendorOrderStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $order->id)
                        ->where('vendor_id', $vendor->id)
                        ->with(['stage' => function($q) {
                            $q->withoutGlobalScopes();
                        }])
                        ->first();
                    
                    return [
                        'id' => $vendor->id,
                        'name' => $vendor->name,
                        'logo_url' => $vendor->logo ? asset('storage/' . $vendor->logo->path) : asset('assets/img/default.png'),
                        'stage' => $vendorOrderStage && $vendorOrderStage->stage ? [
                            'id' => $vendorOrderStage->stage->id,
                            'name' => $vendorOrderStage->stage->getTranslation('name', app()->getLocale()) ?? '-',
                            'color' => $vendorOrderStage->stage->color ?? '#6c757d',
                            'type' => $vendorOrderStage->stage->type ?? null,
                            'slug' => $vendorOrderStage->stage->slug ?? null,
                        ] : null,
                    ];
                })->values();

                $vendorsData = $vendors->map(function ($vendor) {
                    return [
                        'name' => $vendor->name,
                        'logo_url' => $vendor->logo ? asset('storage/' . $vendor->logo->path) : asset('assets/img/default.png')
                    ];
                })->values();

                // Check if order belongs exclusively to current vendor (for vendors only)
                $isExclusiveToCurrentVendor = false;
                if ($currentVendorId) {
                    $orderVendorIds = $order->products->pluck('vendor_id')->unique()->toArray();
                    $isExclusiveToCurrentVendor = count($orderVendorIds) === 1 && in_array($currentVendorId, $orderVendorIds);
                }

                // For vendors: calculate their product total only
                $displayTotalPrice = $order->total_price;
                if ($isVendorUser && $currentVendorId) {
                    // price already includes total (price * quantity), so just sum it
                    $vendorProductTotal = $order->products
                        ->where('vendor_id', $currentVendorId)
                        ->sum('price');
                    
                    // Calculate vendor-specific shipping (sum of shipping_cost for vendor's products)
                    $vendorShipping = $order->products
                        ->where('vendor_id', $currentVendorId)
                        ->sum('shipping_cost');
                    
                    // Get vendor-specific fees and discounts (where vendor_id = current vendor)
                    $vendorSpecificFees = \Modules\Order\app\Models\OrderExtraFeeDiscount::where('order_id', $order->id)
                        ->where('vendor_id', $currentVendorId)
                        ->where('type', 'fee')
                        ->sum('cost');
                    
                    $vendorSpecificDiscounts = \Modules\Order\app\Models\OrderExtraFeeDiscount::where('order_id', $order->id)
                        ->where('vendor_id', $currentVendorId)
                        ->where('type', 'discount')
                        ->sum('cost');
                    
                    // Get shared fees and discounts (where vendor_id is NULL) - distribute proportionally
                    $sharedFees = \Modules\Order\app\Models\OrderExtraFeeDiscount::where('order_id', $order->id)
                        ->whereNull('vendor_id')
                        ->where('type', 'fee')
                        ->sum('cost');
                    
                    $sharedDiscounts = \Modules\Order\app\Models\OrderExtraFeeDiscount::where('order_id', $order->id)
                        ->whereNull('vendor_id')
                        ->where('type', 'discount')
                        ->sum('cost');
                    
                    // Calculate vendor's share of total order (for proportional distribution)
                    $orderProductTotal = $order->products->sum('price');
                    $vendorShareRatio = $orderProductTotal > 0 ? $vendorProductTotal / $orderProductTotal : 0;
                    
                    // Distribute shared fees/discounts proportionally
                    $vendorSharedFees = $sharedFees * $vendorShareRatio;
                    $vendorSharedDiscounts = $sharedDiscounts * $vendorShareRatio;
                    
                    // Total fees and discounts for this vendor
                    $vendorFees = $vendorSpecificFees + $vendorSharedFees;
                    $vendorDiscounts = $vendorSpecificDiscounts + $vendorSharedDiscounts;
                    
                    // Get vendor's promo code and points shares from vendor_order_stages
                    $vendorOrderStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $order->id)
                        ->where('vendor_id', $currentVendorId)
                        ->first();
                    $promoCodeShare = $vendorOrderStage?->promo_code_share ?? 0;
                    $pointsShare = $vendorOrderStage?->points_share ?? 0;
                    
                    // Vendor total = products + shipping + fees - discounts - promo_code - points
                    $displayTotalPrice = $vendorProductTotal + $vendorShipping + $vendorFees - $vendorDiscounts - $promoCodeShare - $pointsShare;
                }

                // Get product stages - filter by vendor if vendor user
                $products = $isVendorUser && $currentVendorId 
                    ? $order->products->where('vendor_id', $currentVendorId)
                    : $order->products;

                // For vendor users, get stage from vendor_order_stages table
                $vendorStage = null;
                if ($isVendorUser && $currentVendorId) {
                    $vendorOrderStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $order->id)
                        ->where('vendor_id', $currentVendorId)
                        ->with('stage')
                        ->first();
                    if ($vendorOrderStage && $vendorOrderStage->stage) {
                        $vendorStage = [
                            'id' => $vendorOrderStage->stage->id,
                            'slug' => $vendorOrderStage->stage->slug,
                            'type' => $vendorOrderStage->stage->type,
                            'name' => $vendorOrderStage->stage->name ?? '-',
                            'color' => $vendorOrderStage->stage->color ?? '#6c757d',
                        ];
                    }
                }

                $productStages = $products->map(function ($orderProduct) {
                    return [
                        'id' => $orderProduct->stage?->id,
                        'slug' => $orderProduct->stage?->slug,
                        'type' => $orderProduct->stage?->type,
                        'name' => $orderProduct->stage?->name ?? '-',
                        'color' => $orderProduct->stage?->color ?? '#6c757d',
                        'product_id' => $orderProduct->id,
                        'product_name' => $orderProduct->name ?? '-',
                    ];
                })->values()->toArray();

                // Check if vendor's products are fully allocated (for vendors only)
                $isFullyAllocated = false;
                if ($isVendorUser && $currentVendorId) {
                    $vendorProducts = $order->products->where('vendor_id', $currentVendorId);
                    if ($vendorProducts->count() > 0) {
                        // Check if all vendor products have fulfillments with status 'allocated' or 'fulfilled'
                        $isFullyAllocated = $vendorProducts->every(function ($product) {
                            $fulfillment = $product->fulfillment;
                            return $fulfillment && in_array($fulfillment->status, ['allocated', 'fulfilled']);
                        });
                    }
                }

                $rowData = [
                    'index' => $index++,
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'customer_email' => $order->customer_email,
                    'customer_phone' => $order->customer_phone ?? '-',
                    'has_quotation' => $order->requestQuotation ? true : false,
                    'quotation_number' => $order->requestQuotation ? $order->requestQuotation->quotation_number : null,
                    'quotation_status' => $order->requestQuotation ? $order->requestQuotation->status : null,
                    'vendor' => $isVendorUser ? [] : $vendorsData, // Hide vendors for vendor users
                    'vendors_with_stages' => $isVendorUser ? [] : $vendorsWithStages, // Vendors with their stages
                    'total_price' => $displayTotalPrice,
                    'total_product_price' => $order->total_product_price . ' ' . currency(),
                    'items_count' => $itemsCount,
                    'product_stages' => $productStages, // Array of product stages
                    'vendor_stage' => $vendorStage, // Vendor's specific stage from vendor_order_stages
                    'payment_type' => $order->payment_type ?? 'cash_on_delivery',
                    'payment_visa_status' => $order->payment_visa_status,
                    'created_at' => $order->created_at,
                    'is_exclusive_to_vendor' => $isExclusiveToCurrentVendor,
                    'is_fully_allocated' => $isFullyAllocated,
                ];

                $data[] = $rowData;
            }

            return response()->json([
                'draw' => intval($request->input('draw', 1)), // Required for DataTables pagination
                'data' => $data,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
                'is_vendor' => $isVendorUser,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred. Please try again later.',
                'errors' => [],
                'data' => [
                    'class' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }

    /**
     * Show create order form
     */
    public function create($lang, $countryCode, Request $request)
    {
        // Load vendor relationships for authenticated user
        if (auth()->check()) {
            auth()->user()->load(['vendorByUser', 'vendorById']);
        }
        
        $quotation = null;
        if ($request->has('quotation_id')) {
            $quotation = RequestQuotation::with(['customer', 'customerAddress.city', 'customerAddress.region', 'customerAddress.subregion', 'customerAddress.country'])
                ->find($request->quotation_id);
        }
        
        return view('order::orders.create', compact('quotation'));
    }

    /**
     * Store a newly created order
     */
    public function store($lang, $countryCode, StoreOrderRequest $request)
    {
        try {
            // Prepare data - fields are already decoded by prepareForValidation in the request
            $data = $request->validated();
            
            // Handle products - may be string or array depending on how it was sent
            if (is_string($data['products'])) {
                $data['products'] = json_decode($data['products'], true) ?? [];
            }
            
            // Handle feesData - may be string or array
            if (isset($data['feesData']) && is_string($data['feesData'])) {
                $data['feesData'] = json_decode($data['feesData'], true) ?? [];
            } else {
                $data['feesData'] = $data['feesData'] ?? [];
            }
            
            // Handle discountsData - may be string or array
            if (isset($data['discountsData']) && is_string($data['discountsData'])) {
                $data['discountsData'] = json_decode($data['discountsData'], true) ?? [];
            } else {
                $data['discountsData'] = $data['discountsData'] ?? [];
            }

            $order = $this->orderService->createOrder($data);

            // Update request quotation status and link order if quotation_id is provided
            if ($request->filled('quotation_id')) {
                $quotation = \Modules\Order\app\Models\RequestQuotation::with('customer')->find($request->input('quotation_id'));
                if ($quotation) {
                    $quotation->update([
                        'status' => \Modules\Order\app\Models\RequestQuotation::STATUS_SENT_OFFER,
                        'order_id' => $order->id,
                        'offer_sent_at' => now(),
                    ]);

                    // Send Firebase notification to customer
                    if ($quotation->customer) {
                        $this->sendQuotationOfferNotification($quotation->customer, $quotation, $order);
                    }
                }
            }

            return response()->json([
                'status' => true,
                'message' => trans('order::order.order_created'),
                'data' => [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'total_price' => $order->total_price,
                    'created_at' => $order->created_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => trans('order::order.error_creating_order'),
                'errors' => [$e->getMessage()],
            ], 422);
        }
    }

    /**
     * Display the specified order
     */
    public function show($lang, $countryCode, $id)
    {
        try {
            $order = $this->orderService->getOrderById($id);
            if (!$order) {
                return abort(404, trans('order::order.order_not_found'));
            }
            
            // Load vendor stages with stage and vendor relationships
            $order->load(['vendorStages.stage', 'vendorStages.vendor', 'products']);
            
            // Ensure vendor stages exist (for old orders created before this feature)
            if ($order->products->count() > 0 && $order->vendorStages->count() == 0) {
                // Get unique vendor IDs from order products
                $vendorIds = $order->products()->distinct()->pluck('vendor_id')->filter();
                
                if ($vendorIds->isNotEmpty()) {
                    // Get the default "new" stage
                    $defaultStage = OrderStage::withoutGlobalScopes()->where('type', 'new')->first();
                    
                    if ($defaultStage) {
                        // Create vendor order stages
                        foreach ($vendorIds as $vendorId) {
                            \Modules\Order\app\Models\VendorOrderStage::create([
                                'order_id' => $order->id,
                                'vendor_id' => $vendorId,
                                'stage_id' => $defaultStage->id,
                            ]);
                        }
                        
                        // Reload vendor stages
                        $order->load(['vendorStages.stage', 'vendorStages.vendor']);
                    }
                }
            }
            
            $isVendorUser = !isAdmin();
            $currentVendorId = null;
            $vendorProducts = null;
            $vendorProductTotal = 0;
            $currentVendorStage = null;
            
            // For vendors: filter products to show only their products
            if ($isVendorUser) {
                $currentVendorId = auth()->user()->vendor?->id;
                if ($currentVendorId) {
                    $vendorProducts = $order->products->filter(function($product) use ($currentVendorId) {
                        return $product->vendor_id == $currentVendorId;
                    });
                    
                    // Calculate vendor total using the same logic as datatable
                    $vendorProductTotal = $this->calculateVendorTotal($order, $currentVendorId, $vendorProducts);
                    
                    // Get current vendor's stage
                    $currentVendorStage = $order->vendorStages->firstWhere('vendor_id', $currentVendorId);
                }
            }
            
            // Get order stages for the change stage modal
            $orderStages = $this->orderStageService->getOrderStagesQuery()->get();
            $orderStages = OrderStageResource::collection($orderStages)->resolve();
            return view('order::orders.show', compact('order', 'isVendorUser', 'vendorProducts', 'vendorProductTotal', 'orderStages', 'currentVendorStage', 'currentVendorId'));
        } catch (\Exception $e) {
            return abort(500, trans('order::order.error_loading_order'));
        }
    }

    /**
     * Display payments/transactions for an order
     */
    public function payments($lang, $countryCode, $id)
    {
        try {
            $order = $this->orderService->getOrderById($id);
            if (!$order) {
                return abort(404, trans('order::order.order_not_found'));
            }
            
            $payments = \Modules\Order\app\Models\Payment::where('order_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Sync order payment status if there's a paid payment but order shows pending
            $hasPaidPayment = $payments->where('status', 'paid')->first();
            if ($hasPaidPayment && $order->payment_visa_status !== 'success') {
                \Modules\Order\app\Models\Order::withoutGlobalScopes()
                    ->where('id', $id)
                    ->update([
                        'payment_visa_status' => 'success',
                        'payment_reference' => $hasPaidPayment->transaction_id,
                    ]);
                $order->payment_visa_status = 'success';
                $order->payment_reference = $hasPaidPayment->transaction_id;
            }
            
            return view('order::orders.payments', compact('order', 'payments'));
        } catch (\Exception $e) {
            return abort(500, trans('order::order.error_loading_order'));
        }
    }

    /**
     * Display printable order view
     */
    public function print($lang, $countryCode, $id, Request $request)
    {
        try {
            $order = $this->orderService->getOrderById($id);
            if (!$order) {
                return abort(404, trans('order::order.order_not_found'));
            }
            
            $isVendorUser = !isAdmin();
            $currentVendorId = null;
            $vendorProducts = null;
            $vendorProductTotal = 0;
            
            // Get selected product IDs from query parameter
            $selectedProductIds = $request->has('products') 
                ? explode(',', $request->input('products')) 
                : [];
            
            // For vendors: filter products to show only their products
            if ($isVendorUser) {
                $currentVendorId = auth()->user()->vendor?->id;
                if ($currentVendorId) {
                    $vendorProducts = $order->products->filter(function($product) use ($currentVendorId, $selectedProductIds) {
                        $matchesVendor = $product->vendor_id == $currentVendorId;
                        
                        // If specific products selected, also filter by those IDs
                        if (!empty($selectedProductIds)) {
                            return $matchesVendor && in_array($product->id, $selectedProductIds);
                        }
                        
                        return $matchesVendor;
                    });
                    // price already includes total (price * quantity), so just sum it
                    $vendorProductTotal = $vendorProducts->sum('price');
                    
                    // Calculate vendor-specific shipping
                    $vendorShipping = $vendorProducts->sum('shipping_cost');
                    
                    // Get vendor's discount and fee shares from vendor_order_stages
                    $vendorOrderStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $order->id)
                        ->where('vendor_id', $currentVendorId)
                        ->first();
                    $promoCodeShare = $vendorOrderStage?->promo_code_share ?? 0;
                    $pointsShare = $vendorOrderStage?->points_share ?? 0;
                    $feesShare = $vendorOrderStage?->fees_share ?? 0;
                    $discountsShare = $vendorOrderStage?->discounts_share ?? 0;
                    
                    // Vendor total = products + shipping + fees - discounts
                    $vendorProductTotal = $vendorProductTotal + $vendorShipping + $feesShare - $promoCodeShare - $pointsShare - $discountsShare;
                }
            } else {
                // For admins: if specific products selected, filter by those IDs
                if (!empty($selectedProductIds)) {
                    $vendorProducts = $order->products->filter(function($product) use ($selectedProductIds) {
                        return in_array($product->id, $selectedProductIds);
                    });
                }
            }
            
            return view('order::orders.print', compact('order', 'isVendorUser', 'vendorProducts', 'vendorProductTotal'));
        } catch (\Exception $e) {
            return abort(500, trans('order::order.error_loading_order'));
        }
    }

    /**
     * Show edit order form
     */
    public function edit($lang, $countryCode, $id)
    {
        $order = $this->orderService->getOrderById($id);
        if (!$order) {
            return abort(404, trans('order::order.order_not_found'));
        }

        // Check if order is in a final stage (deliver, cancel, refund)
        $finalStages = ['deliver', 'cancel', 'refund'];
        if ($order->stage && in_array($order->stage->type, $finalStages)) {
            return redirect()->route('admin.orders.show', $id)
                ->with('error', trans('order::order.cannot_edit_final_stage_order'));
        }

        // Check if vendor can edit this order
        if (!$this->canVendorModifyOrder($order)) {
            return abort(403, trans('order::order.cannot_edit_order'));
        }

        return view('order::orders.edit', compact('order'));
    }

    /**
     * Update the specified order
     */
    public function update($lang, $countryCode, $id, UpdateOrderRequest $request)
    {
        try {
            $order = $this->orderService->getOrderById($id);
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => trans('order::order.order_not_found'),
                ], 404);
            }

            // Check if vendor can update this order
            if (!$this->canVendorModifyOrder($order)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('order::order.cannot_edit_order'),
                ], 403);
            }

            // Prepare data - handle fields that may be string or array
            $data = $request->validated();
            
            // Handle products - may be string or array depending on how it was sent
            if (is_string($data['products'])) {
                $data['products'] = json_decode($data['products'], true) ?? [];
            }
            
            // Handle feesData - may be string or array
            if (isset($data['feesData']) && is_string($data['feesData'])) {
                $data['feesData'] = json_decode($data['feesData'], true) ?? [];
            } else {
                $data['feesData'] = $data['feesData'] ?? [];
            }
            
            // Handle discountsData - may be string or array
            if (isset($data['discountsData']) && is_string($data['discountsData'])) {
                $data['discountsData'] = json_decode($data['discountsData'], true) ?? [];
            } else {
                $data['discountsData'] = $data['discountsData'] ?? [];
            }

            $order = $this->orderService->updateOrder($id, $data);

            return response()->json([
                'status' => true,
                'message' => trans('order::order.order_updated'),
                'data' => [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'total_price' => $order->total_price,
                    'updated_at' => $order->updated_at,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => trans('order::order.error_updating_order'),
                'errors' => [$e->getMessage()],
            ], 422);
        }
    }

    /**
     * Change order stage
     */
    public function changeStage($lang, $countryCode, $id, ChangeOrderStageRequest $request)
    {
        try {
            // Get the order
            $order = $this->orderService->getOrderById($id);
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => trans('order::order.order_not_found'),
                ], 404);
            }

            // Change stage (validation is handled in repository)
            $order = $this->orderService->changeOrderStage($id, $request->stage_id);

            return response()->json([
                'status' => true,
                'message' => trans('order::order.stage_updated_successfully'),
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Change stage for a specific order product
     * Vendors can only change stage for their own products
     */
    public function changeProductStage($lang, $countryCode, $orderProductId, Request $request)
    {
        try {
            $request->validate([
                'stage_id' => 'required|exists:order_stages,id',
            ]);

            $orderProduct = $this->orderService->changeOrderProductStage($orderProductId, $request->stage_id);

            return response()->json([
                'status' => true,
                'message' => trans('order::order.product_stage_updated_successfully'),
                'data' => [
                    'id' => $orderProduct->id,
                    'stage' => [
                        'id' => $orderProduct->stage?->id,
                        'name' => $orderProduct->stage?->getTranslation('name', app()->getLocale()),
                        'color' => $orderProduct->stage?->color,
                        'type' => $orderProduct->stage?->type,
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Change vendor order stage
     * Vendors can only change their own stage
     */
    public function changeVendorStage($lang, $countryCode, $orderId, $vendorId, Request $request)
    {
        try {
            $request->validate([
                'stage_id' => 'required|exists:order_stages,id',
            ]);

            // Verify vendor can change this stage
            $currentVendorId = auth()->user()->vendor?->id;
            if (!$currentVendorId || $currentVendorId != $vendorId) {
                return response()->json([
                    'status' => false,
                    'message' => trans('order::order.unauthorized'),
                ], 403);
            }

            // Check if order is from a quotation and if it's accepted
            $order = \Modules\Order\app\Models\Order::with('requestQuotation')->findOrFail($orderId);
            if ($order->requestQuotation) {
                // Only allow stage change if quotation is accepted
                if ($order->requestQuotation->status !== 'accepted_offer') {
                    return response()->json([
                        'status' => false,
                        'message' => trans('order::order.quotation_not_accepted_yet'),
                    ], 422);
                }
            }

            // Change vendor order stage using repository
            $vendorOrderStage = $this->orderService->changeVendorOrderStage($orderId, $vendorId, $request->stage_id);

            return response()->json([
                'status' => true,
                'message' => trans('order::order.vendor_stage_updated_successfully'),
                'data' => [
                    'id' => $vendorOrderStage->id,
                    'stage' => [
                        'id' => $vendorOrderStage->stage?->id,
                        'name' => $vendorOrderStage->stage?->getTranslation('name', app()->getLocale()),
                        'color' => $vendorOrderStage->stage?->color,
                        'type' => $vendorOrderStage->stage?->type,
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Change stage for all vendors in an order
     * Only admins can use this
     */
    public function changeAllVendorStages($lang, $countryCode, $orderId, Request $request)
    {
        try {
            // Only admins can change all vendor stages
            if (!isAdmin()) {
                return response()->json([
                    'status' => false,
                    'message' => trans('order::order.unauthorized'),
                ], 403);
            }

            $request->validate([
                'stage_id' => 'required|exists:order_stages,id',
            ]);

            $order = Order::with(['vendorStages.stage' => function($q) {
                $q->withoutGlobalScopes();
            }, 'vendorStages.vendor'])->findOrFail($orderId);

            // Check if order is from a quotation and if it's accepted
            if ($order->requestQuotation) {
                if ($order->requestQuotation->status !== 'accepted_offer') {
                    return response()->json([
                        'status' => false,
                        'message' => trans('order::order.quotation_not_accepted_yet'),
                    ], 422);
                }
            }

            $newStage = OrderStage::withoutGlobalScopes()->findOrFail($request->stage_id);
            $updatedCount = 0;
            $skippedCount = 0;
            $errors = [];

            \Log::info('Changing all vendor stages', [
                'order_id' => $orderId,
                'new_stage_id' => $request->stage_id,
                'vendor_stages_count' => $order->vendorStages->count()
            ]);

            // Update each vendor's stage
            foreach ($order->vendorStages as $vendorStage) {
                try {
                    $vendorName = $vendorStage->vendor ? $vendorStage->vendor->name : "Vendor #{$vendorStage->vendor_id}";
                    
                    // Check if transition is allowed
                    $currentStage = $vendorStage->stage;
                    if ($currentStage && $currentStage->isFinalStage()) {
                        $errorMsg = trans('order::order.vendor_already_final_stage', [
                            'vendor' => $vendorName
                        ]);
                        $errors[] = $errorMsg;
                        $skippedCount++;
                        
                        \Log::warning('Vendor stage is final, skipping', [
                            'order_id' => $orderId,
                            'vendor_id' => $vendorStage->vendor_id,
                            'vendor_name' => $vendorName,
                            'current_stage' => $currentStage->slug
                        ]);
                        continue;
                    }

                    // Change vendor order stage
                    $this->orderService->changeVendorOrderStage($orderId, $vendorStage->vendor_id, $request->stage_id);
                    $updatedCount++;
                    
                    \Log::info('Vendor stage updated successfully', [
                        'order_id' => $orderId,
                        'vendor_id' => $vendorStage->vendor_id,
                        'vendor_name' => $vendorName,
                        'new_stage_id' => $request->stage_id
                    ]);
                } catch (\Exception $e) {
                    $vendorName = $vendorStage->vendor ? $vendorStage->vendor->name : "Vendor #{$vendorStage->vendor_id}";
                    $errorMsg = trans('order::order.error_updating_vendor_stage', [
                        'vendor' => $vendorName,
                        'error' => $e->getMessage()
                    ]);
                    $errors[] = $errorMsg;
                    $skippedCount++;
                    
                    \Log::error('Failed to update vendor stage', [
                        'order_id' => $orderId,
                        'vendor_id' => $vendorStage->vendor_id,
                        'vendor_name' => $vendorName,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            if ($updatedCount === 0 && !empty($errors)) {
                return response()->json([
                    'status' => false,
                    'message' => trans('order::order.no_vendors_updated'),
                    'errors' => $errors,
                ], 422);
            }

            $message = $updatedCount > 0 
                ? trans('order::order.vendor_stages_updated', ['updated' => $updatedCount, 'total' => $order->vendorStages->count()])
                : trans('order::order.no_vendors_updated');

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => [
                    'updated_count' => $updatedCount,
                    'skipped_count' => $skippedCount,
                    'total_count' => $order->vendorStages->count(),
                    'errors' => $errors,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in changeAllVendorStages', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk change stage for all products in an order
     * Only admins can use this
     */
    public function bulkChangeProductStages($lang, $countryCode, $id, Request $request)
    {
        try {
            // Only admins can bulk change stages
            if (!isAdmin()) {
                return response()->json([
                    'status' => false,
                    'message' => trans('order::order.unauthorized'),
                ], 403);
            }

            $request->validate([
                'stage_id' => 'required|exists:order_stages,id',
            ]);

            $order = $this->orderService->getOrderById($id);
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => trans('order::order.order_not_found'),
                ], 404);
            }

            // Update all products in the order to the new stage
            $updatedCount = 0;
            foreach ($order->products as $product) {
                try {
                    $this->orderService->changeOrderProductStage($product->id, $request->stage_id);
                    $updatedCount++;
                } catch (\Exception $e) {
                    // Log error but continue with other products
                    \Log::error('Failed to update product stage', [
                        'product_id' => $product->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'status' => true,
                'message' => trans('order::order.bulk_stages_updated_successfully', ['count' => $updatedCount]),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete the specified order
     */
    public function destroy($lang, $countryCode, $id)
    {
        try {
            $order = $this->orderService->getOrderById($id);
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => trans('order::order.order_not_found'),
                ], 404);
            }

            // Check if vendor can delete this order
            if (!$this->canVendorModifyOrder($order)) {
                return response()->json([
                    'success' => false,
                    'message' => trans('order::order.cannot_delete_order'),
                ], 403);
            }

            $this->orderService->deleteOrder($id);

            return response()->json([
                'success' => true,
                'message' => trans('order::order.order_deleted_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('order::order.error_deleting_order'),
                'errors' => [$e->getMessage()]
            ], 422);
        }
    }

    /**
     * Get comprehensive order statistics including totals and stage breakdown
     * @param int|null $vendorId - If null, get stats for all vendors (admin view)
     * @param int|null $countryId - Country ID to filter by
     * @return array - Returns orders_count, total_price, and vendor_stats (stage breakdown)
     */
    private function getOrderStatistics($vendorId, $countryId = null): array
    {
        // Get all order stages
        $stages = OrderStage::withoutGlobalScopes()->get();
        
        // Find the deliver stage
        $deliverStage = $stages->first(function($stage) {
            return $stage->type === 'deliver';
        });
        
        // Build query with country and vendor filters
        $query = \DB::table('orders')
            ->join('order_products', 'orders.id', '=', 'order_products.order_id');
        
        // Filter by country
        if ($countryId) {
            $query->where('orders.country_id', $countryId);
        }
        
        // Filter by vendor if specified
        if ($vendorId) {
            $query->where('order_products.vendor_id', $vendorId);
        }
        
        $vendorOrders = $query->select(
                'orders.id as order_id',
                'orders.total_price',
                'orders.shipping',
                'orders.customer_promo_code_amount',
                'orders.stage_id',
                'order_products.quantity',
                'order_products.price as product_price',
                'order_products.shipping_cost'
            )
            ->get();
        
        // Group by order to avoid counting same order multiple times
        $uniqueOrders = $vendorOrders->groupBy('order_id')->map(function($group) use ($vendorId) {
            // product_price already includes total (price * quantity)
            $vendorProductTotal = $group->sum('product_price');
            
            // For vendors: use vendor-specific shipping (sum of shipping_cost)
            // For admin: use total order shipping
            $shipping = $vendorId 
                ? $group->sum('shipping_cost') 
                : ($group->first()->shipping ?? 0);
            
            $orderId = $group->first()->order_id;
            
            // For vendors: get promo_code_share and points_share from vendor_order_stages
            // These should be SUBTRACTED from vendor total
            $promoCodeShare = 0;
            $pointsShare = 0;
            if ($vendorId) {
                $vendorStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $orderId)
                    ->where('vendor_id', $vendorId)
                    ->first();
                $promoCodeShare = $vendorStage->promo_code_share ?? 0;
                $pointsShare = $vendorStage->points_share ?? 0;
            }
            
            // For admin: use order's promo discount
            $promoDiscount = $vendorId ? 0 : ($group->first()->customer_promo_code_amount ?? 0);
            
            // Calculate total: products + shipping - promo discount - shares (for vendor)
            $total = $vendorProductTotal + $shipping - $promoDiscount - $promoCodeShare - $pointsShare;
            
            return [
                'order_id' => $orderId,
                'total_price' => $vendorId ? $total : $group->first()->total_price,
                'stage_id' => $group->first()->stage_id,
                'products_count' => $group->sum('quantity'),
            ];
        });
        
        // Calculate overall totals
        $totalOrders = $uniqueOrders->count();
        $totalProducts = $uniqueOrders->sum('products_count');
        $totalPrice = $uniqueOrders->sum('total_price');
        
        // Calculate delivery total only for orders with deliver stage
        $deliveredOrders = $deliverStage 
            ? $uniqueOrders->filter(function($order) use ($deliverStage) {
                return $order['stage_id'] == $deliverStage->id;
            })
            : collect([]);
        
        $totalDeliveryValue = $deliveredOrders->sum('total_price');
        
        // Calculate stats by stage
        $stageStats = [];
        foreach ($stages as $stage) {
            $stageOrders = $uniqueOrders->filter(function($order) use ($stage) {
                return $order['stage_id'] == $stage->id;
            });
            
            $stageStats[] = [
                'stage_id' => $stage->id,
                'stage_name' => $stage->getTranslation('name', app()->getLocale()),
                'stage_color' => $stage->color,
                'orders_count' => $stageOrders->count(),
                'products_count' => $stageOrders->sum('products_count'),
                'total_value' => number_format($stageOrders->sum('total_price'), 2),
            ];
        }
        
        // Return comprehensive statistics
        return [
            'orders_count' => $totalOrders,
            'total_price' => number_format($totalPrice, 2),
            'vendor_stats' => [
                'total_orders' => $totalOrders,
                'total_products' => $totalProducts,
                'total_delivery_value' => number_format($totalDeliveryValue, 2),
                'stage_stats' => $stageStats,
            ],
        ];
    }

    /**
     * Send Firebase notification to customer when order is created from quotation
     */
    protected function sendOrderCreatedNotification(Customer $customer, RequestQuotation $quotation, Order $order)
    {
        try {
            $fcmTokens = $customer->fcmTokens()->pluck('fcm_token')->toArray();
            
            if (empty($fcmTokens)) {
                return;
            }

            $firebaseService = app(FirebaseService::class);
            
            $title = __('order::request-quotation.order_created_notification_title');
            $body = __('order::request-quotation.order_created_notification_body', [
                'order_number' => $order->order_number,
            ]);

            $data = [
                'type' => 'quotation_order_created',
                'quotation_id' => (string) $quotation->id,
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
            ];

            $firebaseService->sendToTokens($fcmTokens, $title, $body, null, $data);
        } catch (\Exception $e) {
            \Log::error('Failed to send order created notification: ' . $e->getMessage());
        }
    }

    /**
     * Send Firebase notification to customer when quotation offer is sent with order
     */
    protected function sendQuotationOfferNotification(Customer $customer, RequestQuotation $quotation, Order $order)
    {
        try {
            $fcmTokens = $customer->fcmTokens()->pluck('fcm_token')->toArray();
            
            if (empty($fcmTokens)) {
                return;
            }

            $firebaseService = app(FirebaseService::class);
            
            $title = __('order::request-quotation.quotation_offer_notification_title');
            $body = __('order::request-quotation.quotation_offer_notification_body', [
                'price' => number_format($order->total_price, 2),
            ]);

            $data = [
                'type' => 'quotation_offer_with_order',
                'quotation_id' => (string) $quotation->id,
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
                'total_price' => (string) $order->total_price,
            ];

            $firebaseService->sendToTokens($fcmTokens, $title, $body, null, $data);
        } catch (\Exception $e) {
            \Log::error('Failed to send quotation offer notification: ' . $e->getMessage());
        }
    }

    /**
     * Calculate vendor total including products, shipping, fees, and discounts
     * Fees and discounts are already distributed and stored with vendor_id
     */
    private function calculateVendorTotal($order, $vendorId, $vendorProducts)
    {
        // Sum vendor products (price already includes tax and quantity)
        $vendorProductTotal = $vendorProducts->sum('price');
        
        // Sum vendor shipping
        $vendorShipping = $vendorProducts->sum('shipping_cost');
        
        // Get vendor-specific fees and discounts (already distributed and stored with vendor_id)
        $vendorFees = \Modules\Order\app\Models\OrderExtraFeeDiscount::where('order_id', $order->id)
            ->where('vendor_id', $vendorId)
            ->where('type', 'fee')
            ->sum('cost');
        
        $vendorDiscounts = \Modules\Order\app\Models\OrderExtraFeeDiscount::where('order_id', $order->id)
            ->where('vendor_id', $vendorId)
            ->where('type', 'discount')
            ->sum('cost');
        
        // Get vendor's promo code and points shares from vendor_order_stages
        $vendorOrderStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $order->id)
            ->where('vendor_id', $vendorId)
            ->first();
        $promoCodeShare = $vendorOrderStage?->promo_code_share ?? 0;
        $pointsShare = $vendorOrderStage?->points_share ?? 0;
        
        // Vendor total = products + shipping + fees - discounts - promo_code - points
        return $vendorProductTotal + $vendorShipping + $vendorFees - $vendorDiscounts - $promoCodeShare - $pointsShare;
    }
}
