<?php

namespace Modules\Customer\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Customer\app\Services\CustomerService;
use Modules\Customer\app\Interfaces\CustomerRepositoryInterface;
use Modules\Customer\app\Http\Requests\Dashboard\CustomerRequest;

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

    public function create()
    {
        return view('customer::customer.form');
    }

    public function store(CustomerRequest $request)
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

    public function show($id)
    {
        $customer = $this->customerService->findById([], $id);

        if (!$customer) {
            abort(404);
        }

        return view('customer::customer.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = $this->customerService->findById([],$id);

        if (!$customer) {
            abort(404);
        }

        return view('customer::customer.form', compact('customer'));
    }

    public function update(CustomerRequest $request, $id)
    {
        $customer = $this->customerService->findById([], $id);

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

    public function destroy($id)
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
}
