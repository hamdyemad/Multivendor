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
                'manager_name' => $request->manager_name,
                'company_logo' => $request->company_logo ? asset('storage/' . $request->company_logo) : null,
                'company_logo_storage' => $request->company_logo,
                'status' => $request->status,
                'created_at' => $request->created_at,
                'rejection_reason' => $request->rejection_reason,
                'activities' => $request->activities ? $request->activities->toArray() : [],
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
    public function show($lang, $countryCode, $id)
    {
        $vendorRequest = $this->vendorRequestService->getVendorRequestById($id);
        return view('vendor::vendor-requests.show', compact('vendorRequest'));
    }

    /**
     * Approve vendor request
     */
    public function approve($lang, $countryCode, $id)
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
    public function reject($lang, $countryCode, Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|min:3',
            ]);

            $reason = $validated['reason'];
            $vendorRequest = $this->vendorRequestService->rejectVendorRequest((int)$id, $reason);

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('vendor::vendor.reject_success'),
                    'data' => $vendorRequest
                ]);
            }

            return redirect()->route('admin.vendor-requests.index')
                ->with('success', __('vendor::vendor.reject_success'));
        } catch (\Exception $e) {
            \Log::error('Vendor request rejection failed', [
                'error' => $e->getMessage(),
                'request_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('vendor::vendor.reject_error') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->with('error', __('vendor::vendor.reject_error') . ': ' . $e->getMessage());
        }
    }

    /**
     * Delete vendor request
     */
    public function destroy($lang, $countryCode, $id)
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
