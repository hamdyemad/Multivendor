<?php

namespace Modules\Vendor\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Vendor\app\Http\Resources\Api\VendorRequestResource;
use Modules\Vendor\app\Services\VendorRequestService;

class VendorRequestController extends Controller
{
    public function __construct(protected VendorRequestService $vendorRequestService)
    {
    }

    /**
     * Display a listing of vendor requests
     */
    public function index()
    {
        return view('vendor::vendor-requests.index');
    }

    /**
     * Get vendor requests data for DataTable
     */
    public function datatable(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'email' => $request->get('email'),
        ];

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $vendorRequests = $this->vendorRequestService->getAllVendorRequests($filters, $perPage);

        // Format data for DataTable
        $data = $vendorRequests->map(function ($request, $index) use ($page, $perPage) {
            return [
                'row_number' => (($page - 1) * $perPage) + $index + 1,
                'id' => $request->id,
                'email' => $request->email,
                'phone' => $request->phone,
                'company_name' => $request->company_name,
                'status' => $request->status,
                'activities' => $request->activities->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'name' => $activity->getTranslation('name', app()->getLocale()),
                    ];
                }),
                'created_at' => $request->created_at->format('Y-m-d H:i'),
                'rejection_reason' => $request->rejection_reason,
            ];
        });

        return response()->json([
            'data' => $data,
            'total' => $vendorRequests->total(),
            'per_page' => $vendorRequests->perPage(),
            'current_page' => $vendorRequests->currentPage(),
            'last_page' => $vendorRequests->lastPage(),
            'recordsFiltered' => $vendorRequests->total(),
            'recordsTotal' => $vendorRequests->total(),
        ]);
    }

    /**
     * Show vendor request details
     */
    public function show($id)
    {
        $vendorRequest = $this->vendorRequestService->getVendorRequestById($id);
        return view('vendor::vendor-requests.show', compact('vendorRequest'));
    }

    /**
     * Approve vendor request
     */
    public function approve($id)
    {
        $vendorRequest = $this->vendorRequestService->approveVendorRequest($id);

        return redirect()->back()->with('success', 'Vendor request approved successfully');
    }

    /**
     * Reject vendor request
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $vendorRequest = $this->vendorRequestService->rejectVendorRequest($id, $request->rejection_reason);

        return redirect()->back()->with('success', 'Vendor request rejected successfully');
    }

    /**
     * Delete vendor request
     */
    public function destroy($id)
    {
        $this->vendorRequestService->deleteVendorRequest($id);

        return redirect()->back()->with('success', 'Vendor request deleted successfully');
    }
}
