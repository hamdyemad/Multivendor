<?php

namespace Modules\Customer\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\Customer\app\Services\CustomerService;
use Modules\Vendor\app\Services\VendorService;
use Modules\Customer\app\Interfaces\CustomerRepositoryInterface;
use Modules\Customer\app\Http\Requests\Dashboard\CustomerRequest;
use Modules\SystemSetting\app\Models\PointsSetting;
use Modules\SystemSetting\app\Models\UserPoints;
use Modules\SystemSetting\app\Models\UserPointsTransaction;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService,
        protected VendorService $vendorService,
    ) {
        $this->middleware('can:customers.index')->only(['index', 'datatable']);
        $this->middleware('can:customers.show')->only(['show']);
        $this->middleware('can:customers.create')->only(['create', 'store']);
        $this->middleware('can:customers.edit')->only(['edit', 'update']);
        $this->middleware('can:customers.delete')->only(['destroy']);
        $this->middleware('can:customers.change-status')->only(['changeStatus']);
        $this->middleware('can:customers.change-verification')->only(['changeVerification']);
    }

    /**
     * Check if vendor can manage (edit/delete/change status) this customer
     * Vendors can only manage customers that belong to their vendor
     */
    private function canVendorManageCustomer($customer): bool
    {
        // Admins can manage all customers
        if (isAdmin()) {
            return true;
        }

        // Vendors can only manage customers that belong to their vendor
        if (auth()->check() && auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor && $customer->vendor_id === $vendor->id) {
                return true;
            }
        }

        return false;
    }

    public function index()
    {
        $vendors = [];
        if (isAdmin()) {
            $vendors = \Modules\Vendor\app\Models\Vendor::select('id')->get()->map(function($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->name ?? "Vendor #{$vendor->id}"
                ];
            });
        }

        return view('customer::customer.index', [
            'title' => __('customer::customer.customers_management'),
            'vendors' => $vendors,
        ]);
    }

    public function datatable(Request $request)
    {
        $filters = $request->all();
        $query = $this->customerService->getCustomersQuery($filters);

        $total = $query->count();

        $perPage = $filters['per_page'] ?? 10;
        $page = $filters['page'] ?? 1;

        $customers = $query->with('vendor')->paginate($perPage, ['*'], 'page', $page);

        // Get current vendor ID if user is vendor
        $currentVendorId = null;
        $isVendorUser = false;
        if (!isAdmin() && auth()->check() && auth()->user()->isVendor()) {
            $isVendorUser = true;
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            $currentVendorId = $vendor ? $vendor->id : null;
        }

        $data = $customers->map(function ($customer, $index) use ($page, $perPage, $isVendorUser, $currentVendorId) {
            // Determine if vendor can manage this customer
            $canManage = true;
            if ($isVendorUser) {
                $canManage = $customer->vendor_id === $currentVendorId;
            }

            return [
                'index' => ($page - 1) * $perPage + $index + 1,
                'id' => $customer->id,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'full_name' => $customer->full_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'city_name' => $customer->city?->name ?? '-',
                'region_name' => $customer->region?->name ?? '-',
                'vendor_name' => $customer->vendor?->name ?? null,
                'status' => $customer->status,
                'email_verified_at' => $customer->email_verified_at,
                'created_at' => $customer->created_at,
                'can_manage' => $canManage,
            ];
        })->toArray();

        return response()->json([
            'draw' => intval($request->get('draw', 1)),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
            'total' => $customers->total(),
            'current_page' => $customers->currentPage(),
        ]);
    }

    public function create($lang, $countryCode)
    {
        return view('customer::customer.form');
    }

    public function store($lang, $countryCode, CustomerRequest $request)
    {
        $customer = $this->customerService->createCustomer($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('customer::customer.customer_saved'),
                'redirect' => route('admin.customers.index')
            ]);
        }

        return redirect()->route('admin.customers.index')
            ->with('success', __('customer::customer.customer_saved'));
    }

    public function show($lang, $countryCode, $id)
    {
        $customer = $this->customerService->findById($id, ['addresses']);
        if (!$customer) {
            abort(404);
        }

        // Check if user is vendor
        $isVendor = !isAdmin() && auth()->check() && auth()->user()->isVendor();
        $vendor = null;
        if ($isVendor) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
        }

        // Build orders query - filter by vendor if user is vendor
        // Use withoutGlobalScopes for stage to avoid country filtering
        $ordersQuery = $customer->orders()->with(['stage' => function($q) {
            $q->withoutGlobalScopes();
        }]);
        
        if ($isVendor && $vendor) {
            // Filter orders that have products from this vendor
            $ordersQuery->whereHas('products', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            });
        }

        // Get all filtered orders for statistics
        $filteredOrders = $ordersQuery->get();

        // Calculate total spent based on vendor portion if vendor user
        $totalSpent = 0;
        if ($isVendor && $vendor) {
            foreach ($filteredOrders as $order) {
                // Calculate vendor's portion of this order
                $vendorProducts = $order->products->where('vendor_id', $vendor->id);
                $vendorTotal = $this->calculateVendorOrderTotal($order, $vendor->id, $vendorProducts);
                $totalSpent += $vendorTotal;
            }
        } else {
            $totalSpent = $filteredOrders->sum('total_price');
        }

        // Get all order stages
        $allStages = \Modules\Order\app\Models\OrderStage::withoutGlobalScopes()
            ->active()
            ->orderBy('id')
            ->get();

        // Calculate order counts per stage
        $stageStats = [];
        if ($isVendor && $vendor) {
            // For vendors: count based on vendor_order_stages
            foreach ($allStages as $stage) {
                $count = 0;
                foreach ($filteredOrders as $order) {
                    $vendorStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $order->id)
                        ->where('vendor_id', $vendor->id)
                        ->where('stage_id', $stage->id)
                        ->exists();
                    if ($vendorStage) {
                        $count++;
                    }
                }
                $stageStats[] = [
                    'id' => $stage->id,
                    'name' => $stage->getTranslation('name', app()->getLocale()),
                    'color' => $stage->color ?? '#6c757d',
                    'type' => $stage->type,
                    'count' => $count,
                ];
            }
        } else {
            // For admin: count based on order stage_id
            foreach ($allStages as $stage) {
                $stageStats[] = [
                    'id' => $stage->id,
                    'name' => $stage->getTranslation('name', app()->getLocale()),
                    'color' => $stage->color ?? '#6c757d',
                    'type' => $stage->type,
                    'count' => $filteredOrders->where('stage_id', $stage->id)->count(),
                ];
            }
        }

        // Calculate customer order statistics based on filtered orders
        $orderStats = [
            'total_orders' => $filteredOrders->count(),
            'total_spent' => $totalSpent,
            'average_order_value' => $filteredOrders->count() > 0 ? $totalSpent / $filteredOrders->count() : 0,
            'stages' => $stageStats,
        ];

        // Get orders for the table (latest first) with pagination - rebuild query for pagination
        $ordersQuery = $customer->orders()->with(['stage' => function($q) {
            $q->withoutGlobalScopes();
        }, 'products']);
        
        if ($isVendor && $vendor) {
            $ordersQuery->whereHas('products', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            });
            // Load vendor stages for each order
            $ordersQuery->with(['vendorStages' => function($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id)->with('stage');
            }]);
        }
        
        $orders = $ordersQuery->orderBy('created_at', 'desc')->paginate(10);

        // Calculate vendor portion and get vendor stage for each order if vendor user
        if ($isVendor && $vendor) {
            foreach ($orders as $order) {
                $vendorProducts = $order->products->where('vendor_id', $vendor->id);
                $order->vendor_total = $this->calculateVendorOrderTotal($order, $vendor->id, $vendorProducts);
                
                // Get vendor's stage for this order
                $vendorStage = $order->vendorStages->first();
                if ($vendorStage && $vendorStage->stage) {
                    $order->vendor_stage = $vendorStage->stage;
                }
            }
        }

        // Check if vendor can manage this customer (for showing/hiding edit, delete, status buttons)
        $canManage = $this->canVendorManageCustomer($customer);

        return view('customer::customer.show', compact('customer', 'orderStats', 'orders', 'canManage', 'isVendor'));
    }

    public function edit($lang, $countryCode, $id)
    {
        $customer = $this->customerService->findById($id, []);

        if (!$customer) {
            abort(404);
        }

        // Check if vendor can manage this customer
        if (!$this->canVendorManageCustomer($customer)) {
            abort(403, __('customer::customer.cannot_manage_customer'));
        }

        return view('customer::customer.form', compact('customer'));
    }

    public function update($lang, $countryCode, CustomerRequest $request, $id)
    {
        $customer = $this->customerService->findById($id, []);

        if (!$customer) {
            abort(404);
        }

        // Check if vendor can manage this customer
        if (!$this->canVendorManageCustomer($customer)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('customer::customer.cannot_manage_customer')
                ], 403);
            }
            abort(403, __('customer::customer.cannot_manage_customer'));
        }

        $this->customerService->updateCustomer($id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('customer::customer.customer_updated'),
                'redirect' => route('admin.customers.index')
            ]);
        }

        return redirect()->route('admin.customers.index')
            ->with('success', __('customer::customer.customer_updated'));
    }

    public function destroy($lang, $countryCode, $id)
    {
        try {
            $customer = $this->customerService->findById($id, []);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => __('customer::customer.customer_not_found')
                ], 404);
            }

            // Check if vendor can manage this customer
            if (!$this->canVendorManageCustomer($customer)) {
                return response()->json([
                    'success' => false,
                    'message' => __('customer::customer.cannot_manage_customer')
                ], 403);
            }

            $this->customerService->deleteCustomer($id);
            return response()->json([
                'success' => true,
                'message' => __('customer::customer.customer_deleted'),
                'redirect' => route('admin.customers.index')
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Change customer status (active/inactive)
     */
    public function changeStatus($lang, $countryCode, Request $request, $id)
    {
        try {
            $customer = $this->customerService->findById($id, []);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => __('customer::customer.customer_not_found')
                ], 404);
            }

            // Check if vendor can manage this customer
            if (!$this->canVendorManageCustomer($customer)) {
                return response()->json([
                    'success' => false,
                    'message' => __('customer::customer.cannot_manage_customer')
                ], 403);
            }

            $newStatus = !$customer->status;
            $customer->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => __('customer::customer.status_changed_successfully'),
                'new_status' => $newStatus,
                'status_text' => $newStatus ? __('customer::customer.active') : __('customer::customer.inactive')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('customer::customer.error_changing_status')
            ], 500);
        }
    }

    /**
     * Change customer email verification status
     */
    public function changeVerification($lang, $countryCode, Request $request, $id)
    {
        try {
            $customer = $this->customerService->findById($id, []);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => __('customer::customer.customer_not_found')
                ], 404);
            }

            // Check if vendor can manage this customer
            if (!$this->canVendorManageCustomer($customer)) {
                return response()->json([
                    'success' => false,
                    'message' => __('customer::customer.cannot_manage_customer')
                ], 403);
            }

            $isVerified = !is_null($customer->email_verified_at);
            $newVerification = $isVerified ? null : now();
            $customer->update(['email_verified_at' => $newVerification]);

            return response()->json([
                'success' => true,
                'message' => __('customer::customer.verification_changed_successfully'),
                'is_verified' => !$isVerified,
                'status_text' => !$isVerified ? __('customer::customer.verified') : __('customer::customer.not_verified')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('customer::customer.error_changing_verification')
            ], 500);
        }
    }

    /**
     * Calculate vendor's total for a specific order
     * Includes: products + shipping + fees - discounts - promo_code - points
     */
    private function calculateVendorOrderTotal($order, $vendorId, $vendorProducts)
    {
        // Sum vendor products (price already includes tax and quantity)
        $vendorProductTotal = $vendorProducts->sum('price');
        
        // Sum vendor shipping
        $vendorShipping = $vendorProducts->sum('shipping_cost');
        
        // Get vendor-specific fees and discounts
        $vendorFees = \Modules\Order\app\Models\OrderExtraFeeDiscount::where('order_id', $order->id)
            ->where('vendor_id', $vendorId)
            ->where('type', 'fee')
            ->sum('cost');
        
        $vendorDiscounts = \Modules\Order\app\Models\OrderExtraFeeDiscount::where('order_id', $order->id)
            ->where('vendor_id', $vendorId)
            ->where('type', 'discount')
            ->sum('cost');
        
        // Get vendor's promo code and points shares
        $vendorOrderStage = \Modules\Order\app\Models\VendorOrderStage::where('order_id', $order->id)
            ->where('vendor_id', $vendorId)
            ->first();
        $promoCodeShare = $vendorOrderStage?->promo_code_share ?? 0;
        $pointsShare = $vendorOrderStage?->points_share ?? 0;
        
        // Vendor total = products + shipping + fees - discounts - promo_code - points
        return $vendorProductTotal + $vendorShipping + $vendorFees - $vendorDiscounts - $promoCodeShare - $pointsShare;
    }
}
