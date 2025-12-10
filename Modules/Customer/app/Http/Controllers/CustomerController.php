<?php

namespace Modules\Customer\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Customer\app\Services\CustomerService;
use Modules\Customer\app\Interfaces\CustomerRepositoryInterface;
use Modules\Customer\app\Http\Requests\Dashboard\CustomerRequest;
use Modules\SystemSetting\app\Models\PointsSetting;
use Modules\SystemSetting\app\Models\UserPoints;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService,
    ) {}

    public function index()
    {
        return view('customer::customer.index', [
            'title' => __('customer::customer.customers_management'),
        ]);
    }

    public function datatable(Request $request)
    {
        $filters = $request->all();
        $query = $this->customerService->getCustomersQuery($filters);

        $total = $query->count();

        $perPage = $filters['per_page'] ?? 10;
        $page = $filters['page'] ?? 1;

        $customers = $query->paginate($perPage, ['*'], 'page', $page);

        $data = $customers->map(function ($customer, $index) use ($page, $perPage) {
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
                'status' => $customer->status,
                'email_verified_at' => $customer->email_verified_at,
                'created_at' => $customer->created_at,
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
        $customer = $this->customerService->findById($id, []);
        if (!$customer) {
            abort(404);
        }

        return view('customer::customer.show', compact('customer'));
    }

    public function edit($lang, $countryCode, $id)
    {
        $customer = $this->customerService->findById($id, []);

        if (!$customer) {
            abort(404);
        }

        return view('customer::customer.form', compact('customer'));
    }

    public function update($lang, $countryCode, CustomerRequest $request, $id)
    {
        $customer = $this->customerService->findById($id, []);

        if (!$customer) {
            abort(404);
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
