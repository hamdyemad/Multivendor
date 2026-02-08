<?php

namespace Modules\Order\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Order\app\Models\RequestQuotation;
use Yajra\DataTables\Facades\DataTables;

class RequestQuotationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:request-quotations.index')->only(['index', 'archived', 'datatable']);
        $this->middleware('can:request-quotations.archive')->only(['archive']);
        $this->middleware('can:request-quotations.send-to-vendors')->only(['selectVendors', 'sendToVendors']);
        $this->middleware('can:request-quotations.view-offers')->only(['viewOffers']);
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

            // Apply filters using scopes
            $query->filter([
                'search' => $request->input('search_text'),
                'vendor_status' => $request->input('vendor_status'),
                'created_date_from' => $request->input('created_date_from'),
                'created_date_to' => $request->input('created_date_to'),
            ]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('quotation_number', function ($quotation) {
                    return '<span class="fw-600 text-primary">' . e($quotation->quotation_number) . '</span>';
                })
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
                ->addColumn('vendors_with_status', function ($quotation) {
                    $html = '';
                    
                    // Get all vendors assigned to this quotation with their status
                    $vendors = $quotation->vendors()->with(['vendor', 'order'])->get();
                    
                    if ($vendors->count() > 0) {
                        foreach ($vendors as $vendorQuotation) {
                            $vendor = $vendorQuotation->vendor;
                            if (!$vendor) continue;
                            
                            // Status badges
                            $statusBadges = [
                                'pending' => '<span class="badge badge-warning badge-round">' . __('order::request-quotation.vendor_status_pending') . '</span>',
                                'offer_sent' => '<span class="badge badge-info badge-round">' . __('order::request-quotation.vendor_status_offer_sent') . '</span>',
                                'offer_accepted' => '<span class="badge badge-success badge-round">' . __('order::request-quotation.vendor_status_offer_accepted') . '</span>',
                                'offer_rejected' => '<span class="badge badge-danger badge-round">' . __('order::request-quotation.vendor_status_offer_rejected') . '</span>',
                                'order_created' => '<span class="badge badge-primary badge-round">' . __('order::request-quotation.vendor_status_order_created') . '</span>',
                            ];
                            
                            $statusBadge = $statusBadges[$vendorQuotation->status] ?? '-';
                            
                            // Order number if exists
                            $orderInfo = '';
                            if ($vendorQuotation->order_id && $vendorQuotation->order) {
                                $orderUrl = route('admin.orders.show', ['order' => $vendorQuotation->order_id]);
                                $orderInfo = '<br><small><a href="' . $orderUrl . '" class="text-primary fw-500">' . e($vendorQuotation->order->order_number) . '</a></small>';
                            }
                            
                            $logoUrl = $vendor->logo ? asset('storage/' . $vendor->logo->path) : asset('assets/img/default-vendor.png');
                            
                            $html .= '<div class="mb-1 d-flex align-items-center">';
                            $html .= '<img src="' . $logoUrl . '" alt="' . e($vendor->name) . '" style="width: 24px; height: 24px; border-radius: 50%; margin-right: 6px;">';
                            $html .= '<div><strong>' . e($vendor->name) . '</strong><br>' . $statusBadge . $orderInfo . '</div>';
                            $html .= '</div>';
                        }
                    } else {
                        $html = '<span class="text-muted">' . __('order::request-quotation.no_vendors_assigned') . '</span>';
                    }
                    
                    return $html;
                })
                ->addColumn('created_date', function ($quotation) {
                    return $quotation->created_at ? $quotation->created_at : '-';
                })
                ->addColumn('actions', function ($quotation) use ($isArchived) {
                    $html = '<div class="d-flex gap-2 justify-content-center">';
                    
                    // Assign to Vendors button - for pending status (multi-vendor workflow)
                    if (!$isArchived && $quotation->status === 'pending' && auth()->user()->can('request-quotations.send-to-vendors')) {
                        $selectVendorsUrl = route('admin.request-quotations.select-vendors', [
                            'lang' => app()->getLocale(),
                            'countryCode' => strtolower($quotation->country->code) ?? 'eg',
                            'id' => $quotation->id
                        ]);
                        $html .= '<a href="' . $selectVendorsUrl . '" class="btn btn-sm btn-success" title="' . __('order::request-quotation.select_vendors') . '">
                            <i class="uil uil-users-alt m-0"></i>
                        </a>';
                    }
                    
                    // Create Order button - for pending status (old single-vendor workflow)
                    if (!$isArchived && $quotation->status === 'pending') {
                        $orderUrl = route('admin.orders.create') . '?quotation_id=' . $quotation->id;
                        $html .= '<a href="' . $orderUrl . '" class="btn btn-sm btn-secondary" title="' . __('order::request-quotation.send_quotation_offer') . '">
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
                    // Get vendors with their status and order info
                    $vendorsData = $quotation->vendors()->with(['vendor', 'order'])->get()->map(function($vendorQuotation) {
                        return [
                            'vendor_name' => $vendorQuotation->vendor?->name,
                            'vendor_logo' => $vendorQuotation->vendor && $vendorQuotation->vendor->logo 
                                ? asset('storage/' . $vendorQuotation->vendor->logo->path) 
                                : asset('assets/img/default-vendor.png'),
                            'status' => $vendorQuotation->status,
                            'order_id' => $vendorQuotation->order_id,
                            'order_number' => $vendorQuotation->order?->order_number,
                            'offer_price' => $vendorQuotation->offer_price,
                            'offer_notes' => $vendorQuotation->offer_notes,
                            'offer_sent_at' => $vendorQuotation->offer_sent_at ? $vendorQuotation->offer_sent_at->format('Y-m-d H:i') : null,
                        ];
                    })->toArray();
                    
                    $quotationData = [
                        'id' => $quotation->id,
                        'quotation_number' => $quotation->quotation_number,
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
                        'vendors' => $vendorsData,
                        'created_at' => $quotation->created_at ? $quotation->created_at : '-',
                    ];
                    $html .= '<button type="button" class="btn btn-sm btn-primary btn-view" data-quotation=\'' . json_encode($quotationData) . '\' title="' . __('common.view') . '">
                        <i class="uil uil-eye m-0"></i>
                    </button>';
                    
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['quotation_number', 'customer_info', 'vendors_with_status', 'actions'])
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

    /**
     * Show vendor selection page
     */
    public function selectVendors($lang, $countryCode, $id)
    {
        $quotation = RequestQuotation::with(['customer', 'customerAddress'])->findOrFail($id);
        
        if (!$quotation->canSendToVendors()) {
            return redirect()->back()->with('error', __('order::request-quotation.cannot_send_to_vendors'));
        }

        // Get all active vendors
        $vendors = \Modules\Vendor\app\Models\Vendor::where('active', true)
            ->where('country_id', $quotation->country_id)
            ->get();

        return view('order::request-quotations.select-vendors', compact('quotation', 'vendors'));
    }

    /**
     * Send quotation to selected vendors
     */
    public function sendToVendors(Request $request, $lang, $countryCode, $id)
    {
        $request->validate([
            'vendor_ids' => 'required|array|min:1',
            'vendor_ids.*' => 'exists:vendors,id',
        ]);

        $quotation = RequestQuotation::findOrFail($id);
        
        if (!$quotation->canSendToVendors()) {
            return response()->json([
                'status' => false,
                'message' => __('order::request-quotation.cannot_send_to_vendors'),
            ], 400);
        }

        try {
            DB::beginTransaction();

            $vendorIds = $request->input('vendor_ids');
            $createdCount = 0;

            foreach ($vendorIds as $vendorId) {
                // Create request_quotation_vendor record
                $quotationVendor = \Modules\Order\app\Models\RequestQuotationVendor::create([
                    'request_quotation_id' => $quotation->id,
                    'vendor_id' => $vendorId,
                    'status' => \Modules\Order\app\Models\RequestQuotationVendor::STATUS_PENDING,
                ]);

                // Send notification to vendor using AdminNotification
                $vendor = \Modules\Vendor\app\Models\Vendor::find($vendorId);
                if ($vendor && $vendor->user) {
                    \App\Models\AdminNotification::notify(
                        type: 'vendor_quotation_request',
                        title: 'order::request-quotation.notification_vendor_new_request_title',
                        description: 'order::request-quotation.notification_vendor_new_request_message',
                        url: route('admin.vendor.request-quotations.show', [
                            'lang' => app()->getLocale(),
                            'countryCode' => $quotation->country->code ?? 'eg',
                            'id' => $quotationVendor->id,
                        ]),
                        icon: 'uil-file-question-alt',
                        color: 'info',
                        notifiable: $quotationVendor,
                        data: [
                            'customer' => $quotation->customer_name,
                            'quotation_vendor_id' => $quotationVendor->id,
                            'quotation_id' => $quotation->id,
                        ],
                        userId: $vendor->user->id,
                        vendorId: $vendorId
                    );
                }

                $createdCount++;
            }

            // Update quotation status
            $quotation->update(['status' => RequestQuotation::STATUS_SENT_TO_VENDORS]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => __('order::request-quotation.sent_to_vendors_successfully', ['count' => $createdCount]),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'message' => __('common.error_occurred') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View vendors and their offers
     */
    public function viewOffers($lang, $countryCode, $id)
    {
        $quotation = RequestQuotation::with([
            'customer',
            'customerAddress',
            'vendors.vendor',
            'vendors.order'
        ])->findOrFail($id);

        return view('order::request-quotations.view-offers', compact('quotation'));
    }
}
