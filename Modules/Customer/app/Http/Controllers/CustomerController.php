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
        $this->middleware('can:customers.edit')->only(['edit', 'update', 'changeStatus', 'changeVerification']);
        $this->middleware('can:customers.delete')->only(['destroy']);
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

        // Build orders query - filter by vendor if user is vendor
        $ordersQuery = $customer->orders()->with('stage');
        
        if (!isAdmin() && auth()->check() && auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor) {
                // Filter orders that have products from this vendor
                $ordersQuery->whereHas('products', function ($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id);
                });
            }
        }

        // Get all filtered orders for statistics
        $filteredOrders = $ordersQuery->get();

        // Calculate customer order statistics based on filtered orders
        $orderStats = [
            'total_orders' => $filteredOrders->count(),
            'total_spent' => $filteredOrders->sum('total_price'),
            'delivered_orders' => $filteredOrders->filter(fn($o) => $o->stage && $o->stage->type === 'deliver')->count(),
            'pending_orders' => $filteredOrders->filter(fn($o) => $o->stage && in_array($o->stage->type, ['new', 'in_progress']))->count(),
            'cancelled_orders' => $filteredOrders->filter(fn($o) => $o->stage && $o->stage->type === 'cancel')->count(),
            'average_order_value' => $filteredOrders->count() > 0 ? $filteredOrders->sum('total_price') / $filteredOrders->count() : 0,
        ];

        // Get orders for the table (latest first) with pagination - rebuild query for pagination
        $ordersQuery = $customer->orders()->with('stage');
        
        if (!isAdmin() && auth()->check() && auth()->user()->isVendor()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if ($vendor) {
                $ordersQuery->whereHas('products', function ($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id);
                });
            }
        }
        
        $orders = $ordersQuery->orderBy('created_at', 'desc')->paginate(10);

        // Check if vendor can manage this customer (for showing/hiding edit, delete, status buttons)
        $canManage = $this->canVendorManageCustomer($customer);

        return view('customer::customer.show', compact('customer', 'orderStats', 'orders', 'canManage'));
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
}
