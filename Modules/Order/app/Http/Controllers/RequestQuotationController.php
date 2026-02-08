<?php

namespace Modules\Order\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Order\app\Models\RequestQuotation;
use Yajra\DataTables\Facades\DataTables;

class RequestQuotationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:request-quotations.index')->only(['index', 'archived', 'datatable']);
        $this->middleware('can:request-quotations.archive')->only(['archive']);
    }

    public function index()
    {
        return view('order::request-quotations.index', ['isArchived' => false]);
    }

    public function archived()
    {
        return view('order::request-quotations.index', ['isArchived' => true]);
    }

    public function datatable(Request $request)
    {
        try {
            $isArchived = $request->boolean('is_archived');
            
            $query = RequestQuotation::with(['order', 'customer', 'customerAddress.city', 'customerAddress.region', 'customerAddress.subregion', 'customerAddress.country'])->latest();
            
            if ($isArchived) {
                $query->archived();
            } else {
                $query->notArchived();
            }

            // Apply filters
            if ($request->filled('search_text')) {
                $search = $request->input('search_text');
                $query->where(function ($q) use ($search) {
                    $q->where('notes', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($q2) use ($search) {
                            $q2->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        })
                        ->orWhereHas('customerAddress', function ($q2) use ($search) {
                            $q2->where('address', 'like', "%{$search}%");
                        })
                        ->orWhereHas('order', function ($q2) use ($search) {
                            $q2->where('order_number', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->filled('status') && $request->input('status') !== 'all') {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('created_date_from')) {
                $query->whereDate('created_at', '>=', $request->input('created_date_from'));
            }

            if ($request->filled('created_date_to')) {
                $query->whereDate('created_at', '<=', $request->input('created_date_to'));
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_info', function ($quotation) {
                    $name = $quotation->customer?->full_name ?? '-';
                    $email = $quotation->customer?->email ?? '-';
                    $phone = $quotation->customer?->phone ?? '-';
                    
                    // Build location parts
                    $locationParts = [];
                    if ($quotation->customerAddress) {
                        if ($quotation->customerAddress->city) {
                            $locationParts[] = $quotation->customerAddress->city->name;
                        }
                        if ($quotation->customerAddress->region) {
                            $locationParts[] = $quotation->customerAddress->region->name;
                        }
                    }
                    $location = !empty($locationParts) ? implode(', ', $locationParts) : '';
                    
                    $html = '<div>';
                    $html .= '<strong>' . e($name) . '</strong>';
                    $html .= '<br><small class="text-muted"><i class="uil uil-phone me-1"></i>' . e($phone) . '</small>';
                    $html .= '<br><small class="text-muted"><i class="uil uil-envelope me-1"></i>' . e($email) . '</small>';
                    if ($location) {
                        $html .= '<br><small class="text-muted"><i class="uil uil-map-marker me-1"></i>' . e($location) . '</small>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('status_badge', function ($quotation) {
                    $badges = [
                        'pending' => '<span class="badge badge-warning badge-round">' . __('order::request-quotation.status_pending') . '</span>',
                        'sent_offer' => '<span class="badge badge-info badge-round">' . __('order::request-quotation.status_sent_offer') . '</span>',
                        'accepted_offer' => '<span class="badge badge-success badge-round">' . __('order::request-quotation.status_accepted_offer') . '</span>',
                        'rejected_offer' => '<span class="badge badge-danger badge-round">' . __('order::request-quotation.status_rejected_offer') . '</span>',
                        'order_created' => '<span class="badge badge-primary badge-round">' . __('order::request-quotation.status_order_created') . '</span>',
                        'archived' => '<span class="badge badge-secondary badge-round">' . __('order::request-quotation.status_archived') . '</span>',
                    ];
                    return $badges[$quotation->status] ?? '-';
                })
                ->addColumn('order_number', function ($quotation) {
                    if ($quotation->order) {
                        $orderUrl = route('admin.orders.show', ['order' => $quotation->order_id]);
                        return '<a href="' . $orderUrl . '" class="text-primary fw-500">' . e($quotation->order->order_number) . '</a>';
                    }
                    return '-';
                })
                ->addColumn('created_date', function ($quotation) {
                    return $quotation->created_at ? $quotation->created_at : '-';
                })
                ->addColumn('actions', function ($quotation) use ($isArchived) {
                    $html = '<div class="d-flex gap-2 justify-content-center">';
                    
                    // Create Order button - for pending status (new workflow: admin creates order as offer)
                    if (!$isArchived && $quotation->status === 'pending') {
                        $orderUrl = route('admin.orders.create') . '?quotation_id=' . $quotation->id;
                        $html .= '<a href="' . $orderUrl . '" class="btn btn-sm btn-success" title="' . __('order::request-quotation.send_quotation_offer') . '">
                            <i class="uil uil-file-plus m-0"></i>
                        </a>';
                    }
                    
                    // View Order button - for sent_offer, accepted_offer, rejected_offer, order_created status with order
                    if (!$isArchived && $quotation->order_id && in_array($quotation->status, ['sent_offer', 'accepted_offer', 'rejected_offer', 'order_created'])) {
                        $orderUrl = route('admin.orders.show', ['order' => $quotation->order_id]);
                        $html .= '<a href="' . $orderUrl . '" class="btn btn-sm btn-info" title="' . __('order::request-quotation.view_order') . '">
                            <i class="uil uil-file-alt m-0"></i>
                        </a>';
                    }
                    
                    // Download file button
                    if ($quotation->file) {
                        $html .= '<a href="' . asset('storage/' . $quotation->file) . '" class="btn btn-sm btn-info" download title="' . __('order::request-quotation.download_file') . '">
                            <i class="uil uil-download-alt m-0"></i>
                        </a>';
                    }
                    
                    // Archive button - only for pending status (with permission check)
                    if (!$isArchived && $quotation->status === 'pending') {
                        if(auth()->user()->can('request-quotations.archive')) {
                            $html .= '<button type="button" class="btn btn-sm btn-warning btn-archive" data-id="' . $quotation->id . '" title="' . __('order::request-quotation.archive') . '">
                                <i class="uil uil-archive m-0"></i>
                            </button>';
                        }
                    }

                    // View details button
                    $quotationData = [
                        'id' => $quotation->id,
                        'customer_name' => $quotation->customer?->full_name,
                        'customer_email' => $quotation->customer?->email,
                        'customer_phone' => $quotation->customer?->phone,
                        'full_address' => $quotation->full_address,
                        'address_title' => $quotation->customerAddress?->title,
                        'city' => $quotation->customerAddress?->city?->name,
                        'region' => $quotation->customerAddress?->region?->name,
                        'subregion' => $quotation->customerAddress?->subregion?->name,
                        'country' => $quotation->customerAddress?->country?->name,
                        'notes' => $quotation->notes,
                        'status' => $quotation->status,
                        'order_id' => $quotation->order_id,
                        'order_number' => $quotation->order?->order_number,
                        'order_total' => $quotation->order?->total_price,
                        'offer_sent_at' => $quotation->offer_sent_at,
                        'offer_responded_at' => $quotation->offer_responded_at,
                        'created_at' => $quotation->created_at ? $quotation->created_at : '-',
                    ];
                    $html .= '<button type="button" class="btn btn-sm btn-primary btn-view" data-quotation=\'' . json_encode($quotationData) . '\' title="' . __('common.view') . '">
                        <i class="uil uil-eye m-0"></i>
                    </button>';
                    
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['customer_info', 'status_badge', 'order_number', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function archive($lang, $countryCode, $id)
    {
        $quotation = RequestQuotation::findOrFail($id);
        $quotation->update(['status' => RequestQuotation::STATUS_ARCHIVED]);

        return response()->json([
            'status' => true,
            'message' => __('order::request-quotation.archived_successfully'),
        ]);
    }
}
