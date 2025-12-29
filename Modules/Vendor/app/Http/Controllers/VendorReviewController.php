<?php

namespace Modules\Vendor\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CatalogManagement\app\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Vendor\app\Models\Vendor;
use Yajra\DataTables\Facades\DataTables;

class VendorReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:vendor-reviews.index')->only(['index', 'datatable']);
        $this->middleware('can:vendor-reviews.approve')->only(['approve']);
        $this->middleware('can:vendor-reviews.reject')->only(['reject']);
    }

    /**
     * Display a listing of vendor reviews
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        return view('vendor::vendor-review.index', compact('status'));
    }

    /**
     * Get reviews data for DataTables
     */
    public function datatable(Request $request)
    {
        $status = $request->input('status');
        $star = $request->input('star');
        $search = $request->input('search');
        $createdFrom = $request->input('created_date_from');
        $createdTo = $request->input('created_date_to');

        $query = Review::with(['customer'])
            ->latest()
            ->where('reviewable_type', Vendor::class);

        // Filter by status
        if ($status && $status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by rating
        if ($star && $star !== '') {
            $query->where('star', $star);
        }

        // Search by vendor name or customer name
        if ($search && $search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($subQ) use ($search) {
                    $subQ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            });
        }

        // Filter by date range
        if ($createdFrom) {
            $query->whereDate('created_at', '>=', $createdFrom);
        }
        if ($createdTo) {
            $query->whereDate('created_at', '<=', $createdTo);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('vendor_info', function ($review) {
                $vendor = Vendor::withoutCountryFilter()->with('logo')->find($review->reviewable_id);
                $logoUrl = $vendor && $vendor->logo ? asset('storage/' . $vendor->logo->path) : asset('assets/img/default.png');
                $vendorName = truncateString($vendor->name ?? '-');
                return '<div class="d-flex align-items-center gap-2">
                    <img src="' . $logoUrl . '" alt="' . ($vendor->name ?? 'Vendor') . '" class="rounded-circle" style="width: 40px; height: 40px;">
                    <span>' . $vendorName . '</span>
                </div>';
            })
            ->addColumn('customer_name', function ($review) {
                return truncateString($review->customer->full_name ?? '-');
            })
            ->addColumn('stars', function ($review) {
                $html = '<div class="d-flex align-items-center justify-content-center">';
                $html .= '<div class="rating-badge">';
                $html .= '<div class="d-flex">';
                for ($i = 0; $i < 5; $i++) {
                    if ($i < $review->star) {
                        $html .= '<i class="uil uil-star text-warning" style="font-size: 16px;"></i>';
                    } else {
                        $html .= '<i class="uil uil-star text-muted opacity-50" style="font-size: 16px;"></i>';
                    }
                }
                $html .= '</div>';
                $html .= '<span class="ms-2 fw-bold text-dark fs-13">' . $review->star . '.0</span>';
                $html .= '</div>';
                $html .= '</div>';
                return $html;
            })
            ->addColumn('status_info', function ($review) {
                $statusBadge = '';
                if ($review->status === 'pending') {
                    $statusBadge = '<span class="badge badge-warning badge-round badge-lg">' . __('common.pending') . '</span>';
                } elseif ($review->status === 'approved') {
                    $statusBadge = '<span class="badge badge-success badge-round badge-lg">' . __('common.approved') . '</span>';
                } else {
                    $statusBadge = '<span class="badge badge-danger badge-round badge-lg">' . __('common.rejected') . '</span>';
                }

                $html = '<div>' . $statusBadge . '</div>';
                if ($review->status === 'rejected' && $review->rejection_reason) {
                    $html .= '<small class="text-danger mt-2 d-block"><strong>' . __('vendor::vendor-review.rejection_reason') . ':</strong> ' . Str::limit($review->rejection_reason, 50) . '</small>';
                }
                return $html;
            })
            ->addColumn('created_date', function ($review) {
                return $review->created_at;
            })
            ->addColumn('actions', function ($review) {
                $vendor = Vendor::withoutCountryFilter()->find($review->reviewable_id);
                $reviewData = [
                    'vendor_name' => truncateString($vendor->name ?? '-'),
                    'customer_name' => $review->customer ? $review->customer->full_name : '-',
                    'star' => $review->star,
                    'review' => truncateString($review->review),
                    'status' => $review->status,
                    'rejection_reason' => $review->rejection_reason
                ];

                $html = '<div class="d-flex gap-2 justify-content-center">';
                if ($review->status === 'pending') {
                    if (auth()->user()->can('vendor-reviews.approve')) {
                        $html .= '<button type="button" class="btn btn-sm btn-success btn-approve-review" data-review-id="' . $review->id . '" title="' . __('common.approve') . '">
                            <i class="uil uil-check m-0"></i>
                        </button>';
                    }
                    if (auth()->user()->can('vendor-reviews.reject')) {
                        $html .= '<button type="button" class="btn btn-sm btn-danger btn-reject-review" data-review-id="' . $review->id . '" data-review=\'' . json_encode($reviewData) . '\' title="' . __('common.reject') . '">
                            <i class="uil uil-times m-0"></i>
                        </button>';
                    }
                }

                $html .= '<button type="button" class="btn btn-sm btn-info btn-view-review" data-review-id="' . $review->id . '" data-review=\'' . json_encode($reviewData) . '\' title="' . __('common.view') . '">
                    <i class="uil uil-eye m-0"></i>
                </button>';
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['vendor_info', 'stars', 'status_info', 'actions'])
            ->make(true);
    }

    /**
     * Approve a review
     */
    public function approve($lang, $countryCode, Review $review)
    {
        $review->update(['status' => Review::STATUS_APPROVED]);

        return redirect()->back()->with('success', __('vendor::vendor-review.review_approved'));
    }

    /**
     * Reject a review with reason
     */
    public function reject($lang, $countryCode, Review $review, Request $request)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $review->update([
            'status' => Review::STATUS_REJECTED,
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        return redirect()->back()->with('success', __('vendor::vendor-review.review_rejected'));
    }
}
