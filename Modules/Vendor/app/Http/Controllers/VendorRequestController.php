<?php

namespace Modules\Vendor\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CategoryManagment\app\Models\Activity;
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
        $activities = Activity::all()->map(function ($activity) {
            return [
                'id' => $activity->id,
                'name' => $activity->getTranslation('name', app()->getLocale()),
            ];
        });

        return view('vendor::vendor-requests.index', compact('activities'));
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
            'activity_id' => $request->get('activity_id'),
            'created_date_from' => $request->get('created_date_from'),
            'created_date_to' => $request->get('created_date_to'),
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
                'created_at' => $request->created_at,
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
        try {
            $vendorRequest = $this->vendorRequestService->approveVendorRequest($id);
            return redirect()->route('admin.vendor-requests.index')
                ->with('success', __('vendor::vendor.approve_success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('vendor::vendor.approve_error'));
        }
    }

    /**
     * Reject vendor request
     */
    public function reject(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'reason' => 'nullable|string|min:3',
            ]);

            $vendorRequest = $this->vendorRequestService->rejectVendorRequest($id, $validated['reason']);

            return redirect()->route('admin.vendor-requests.index')
                ->with('success', __('vendor::vendor.reject_success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('vendor::vendor.reject_error'));
        }
    }

    /**
     * Delete vendor request
     */
    public function destroy($id)
    {
        try {
            $this->vendorRequestService->deleteVendorRequest($id);

            return redirect()->route('admin.vendor-requests.index')
                ->with('success', __('vendor::vendor.archive_success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('vendor::vendor.archive_error'));
        }
    }
}
