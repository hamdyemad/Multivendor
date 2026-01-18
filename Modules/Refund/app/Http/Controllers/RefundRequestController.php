<?php

namespace Modules\Refund\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Refund\app\Models\RefundRequest;
use Modules\Refund\app\Http\Requests\RejectRefundRequest;
use Modules\Refund\app\Http\Requests\ChangeRefundStatusRequest;
use Modules\Refund\app\Http\Requests\UpdateRefundNotesRequest;

class RefundRequestController extends Controller
{
    /**
     * Display a listing of refund requests
     */
    public function index(Request $request)
    {
        return view('refund::refund-requests.index');
    }
    
    /**
     * DataTable endpoint for refund requests
     */
    public function datatable(Request $request)
    {
        try {
            // Get pagination parameters from DataTables
            $perPage = isset($request->per_page) && $request->per_page > 0 ? (int)$request->per_page : 10;
            $start = isset($request->start) && $request->start >= 0 ? (int)$request->start : 0;
            $page = $perPage > 0 ? floor($start / $perPage) + 1 : 1;

            // Build filters array
            $filters = [
                'status' => $request->status_filter ?? null,
                'search' => $request->search ?? null,
                'date_from' => $request->created_date_from ?? null,
                'date_to' => $request->created_date_to ?? null,
            ];
            
            // Add vendor filter if not admin
            if (!isAdmin()) {
                $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
                if ($vendor) {
                    $filters['current_vendor_id'] = $vendor->id;
                }
            }

            // Build query with scopeFilters
            $query = RefundRequest::with(['order', 'customer', 'vendor', 'items.orderProduct'])
                ->filter($filters);
            
            // Get total and filtered counts
            $totalRecords = RefundRequest::count();
            $filteredRecords = $query->count();
            
            // Get paginated results
            $refundRequests = $query->latest()->paginate($perPage, ['*'], 'page', $page);
            
            // Format data for DataTables
            $data = [];
            $index = $start + 1;
            
            foreach ($refundRequests as $refund) {
                $statusBadges = [
                    'pending' => '<span class="badge badge-warning badge-round badge-lg"><i class="uil uil-clock"></i> ' . trans('refund::refund.statuses.pending') . '</span>',
                    'approved' => '<span class="badge badge-info badge-round badge-lg"><i class="uil uil-check"></i> ' . trans('refund::refund.statuses.approved') . '</span>',
                    'in_progress' => '<span class="badge badge-primary badge-round badge-lg"><i class="uil uil-sync"></i> ' . trans('refund::refund.statuses.in_progress') . '</span>',
                    'picked_up' => '<span class="badge badge-secondary badge-round badge-lg"><i class="uil uil-package"></i> ' . trans('refund::refund.statuses.picked_up') . '</span>',
                    'refunded' => '<span class="badge badge-success badge-round badge-lg"><i class="uil uil-check-circle"></i> ' . trans('refund::refund.statuses.refunded') . '</span>',
                    'rejected' => '<span class="badge badge-danger badge-round badge-lg"><i class="uil uil-times-circle"></i> ' . trans('refund::refund.statuses.rejected') . '</span>',
                ];
                
                $data[] = [
                    'index' => $index++,
                    'refund_number' => $refund->refund_number,
                    'order_number' => $refund->order ? $refund->order->order_number : '-',
                    'customer_name' => $refund->customer ? $refund->customer->name : '-',
                    'vendor_name' => $refund->vendor ? $refund->vendor->name : '-',
                    'total_amount' => number_format($refund->total_refund_amount, 2),
                    'status' => $statusBadges[$refund->status] ?? $refund->status,
                    'created_at' => $refund->created_at->format('Y-m-d H:i'),
                    'id' => $refund->id,
                ];
            }
            
            return response()->json([
                'draw' => intval($request->draw ?? 1),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'draw' => intval($request->draw ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Display the specified refund request
     */
    public function show(RefundRequest $refundRequest)
    {
        // Check if vendor can view this refund request
        if (!isAdmin()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if (!$vendor || $refundRequest->vendor_id != $vendor->id) {
                abort(403, 'Unauthorized');
            }
        }
        
        $refundRequest->load(['order', 'customer', 'vendor', 'items.orderProduct.vendorProduct.product']);
        
        return view('refund::refund-requests.show', compact('refundRequest'));
    }
    
    /**
     * Approve refund request
     */
    public function approve(RefundRequest $refundRequest)
    {
        // Check if vendor can approve this refund request
        if (!isAdmin()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if (!$vendor || $refundRequest->vendor_id != $vendor->id) {
                abort(403, 'Unauthorized');
            }
        }
        
        if ($refundRequest->status != 'pending') {
            return back()->with('error', trans('refund::refund.errors.cannot_approve'));
        }
        
        $refundRequest->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
        
        // TODO: Send notification to customer
        
        return back()->with('success', trans('refund::refund.messages.approved_successfully'));
    }
    
    /**
     * Reject refund request
     */
    public function reject(RejectRefundRequest $request, RefundRequest $refundRequest)
    {
        // Check if vendor can reject this refund request
        if (!isAdmin()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if (!$vendor || $refundRequest->vendor_id != $vendor->id) {
                abort(403, 'Unauthorized');
            }
        }
        
        if ($refundRequest->status != 'pending') {
            return back()->with('error', trans('refund::refund.errors.cannot_reject'));
        }
        
        $refundRequest->update([
            'status' => 'rejected',
            'vendor_notes' => $request->rejection_reason,
        ]);
        
        // TODO: Send notification to customer
        
        return back()->with('success', trans('refund::refund.messages.rejected_successfully'));
    }
    
    /**
     * Change refund request status
     */
    public function changeStatus(ChangeRefundStatusRequest $request, RefundRequest $refundRequest)
    {
        // Check if vendor can change status
        if (!isAdmin()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if (!$vendor || $refundRequest->vendor_id != $vendor->id) {
                abort(403, 'Unauthorized');
            }
        }
        
        $refundRequest->update([
            'status' => $request->status,
        ]);
        
        // TODO: Send notification to customer
        
        return back()->with('success', trans('refund::refund.messages.status_updated'));
    }
    
    /**
     * Update vendor notes
     */
    public function updateNotes(UpdateRefundNotesRequest $request, RefundRequest $refundRequest)
    {
        // Check if vendor can update notes
        if (!isAdmin()) {
            $vendor = auth()->user()->vendorByUser ?? auth()->user()->vendorById;
            if (!$vendor || $refundRequest->vendor_id != $vendor->id) {
                abort(403, 'Unauthorized');
            }
        }
        
        $notesField = isAdmin() ? 'admin_notes' : 'vendor_notes';
        
        $refundRequest->update([
            $notesField => $request->notes,
        ]);
        
        return back()->with('success', trans('refund::refund.messages.notes_updated'));
    }
}
