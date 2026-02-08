<?php

namespace Modules\Order\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Order\app\Models\RequestQuotation;
use Modules\Order\app\Models\RequestQuotationVendor;
use Modules\Order\app\Notifications\CustomerOfferReceivedNotification;
use Yajra\DataTables\Facades\DataTables;

class VendorRequestQuotationController extends Controller
{
    public function __construct()
    {
        // No permission checks - available to all vendors
    }

    /**
     * Display list of quotations sent to this vendor
     */
    public function index()
    {
        return view('order::vendor.request-quotations.index');
    }

    /**
     * DataTable for vendor quotations
     */
    public function datatable(Request $request)
    {
        try {
            $vendorId = auth()->user()->vendorByUser?->id ?? auth()->user()->vendorById?->id ?? null;
            
            if (!$vendorId) {
                return response()->json([
                    'error' => true,
                    'message' => 'Vendor not found',
                ], 403);
            }

            $query = RequestQuotationVendor::with([
                'requestQuotation.customer',
                'requestQuotation.customerAddress.city',
                'requestQuotation.customerAddress.region',
                'order'
            ])
            ->filter([
                'vendor_id' => $vendorId,
                'search' => $request->input('search_text'),
                'status' => $request->input('status'),
                'created_date_from' => $request->input('created_date_from'),
                'created_date_to' => $request->input('created_date_to'),
            ])
            ->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('quotation_number', function ($quotationVendor) {
                    $quotation = $quotationVendor->requestQuotation;
                    return '<span class="fw-bold text-primary">' . e($quotation->quotation_number ?? '-') . '</span>';
                })
                ->addColumn('customer_info', function ($quotationVendor) {
                    $quotation = $quotationVendor->requestQuotation;
                    $name = $quotation->customer?->full_name ?? '-';
                    $email = $quotation->customer?->email ?? '-';
                    $phone = $quotation->customer?->phone ?? '-';
                    
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
                ->addColumn('status_badge', function ($quotationVendor) {
                    $color = $quotationVendor->status_badge_color;
                    $label = $quotationVendor->status_label;
                    return '<span class="badge badge-' . $color . ' badge-round">' . $label . '</span>';
                })
                ->addColumn('order_number', function ($quotationVendor) {
                    if ($quotationVendor->order) {
                        $orderUrl = route('admin.orders.show', [
                            'lang' => app()->getLocale(),
                            'countryCode' => $quotationVendor->requestQuotation->country->code ?? 'eg',
                            'order' => $quotationVendor->order_id
                        ]);
                        return '<a href="' . $orderUrl . '" class="text-primary fw-500">' . e($quotationVendor->order->order_number) . '</a>';
                    }
                    return '-';
                })
                ->addColumn('created_date', function ($quotationVendor) {
                    return $quotationVendor->created_at ? $quotationVendor->created_at->format('Y-m-d H:i') : '-';
                })
                ->addColumn('actions', function ($quotationVendor) {
                    $html = '<div class="d-flex gap-2 justify-content-center">';
                    
                    // View details button
                    $viewUrl = route('admin.vendor.request-quotations.show', [
                        'lang' => app()->getLocale(),
                        'countryCode' => $quotationVendor->requestQuotation->country->code ?? 'eg',
                        'id' => $quotationVendor->id
                    ]);
                    $html .= '<a href="' . $viewUrl . '" class="btn btn-sm btn-primary" title="' . __('common.view') . '">
                        <i class="uil uil-eye m-0"></i>
                    </a>';
                    
                    // Send Offer button (Create Order) - only if no order created yet
                    if (!$quotationVendor->order_id) {
                        $createOrderUrl = route('admin.orders.create', [
                            'lang' => app()->getLocale(),
                            'countryCode' => $quotationVendor->requestQuotation->country->code ?? 'eg',
                            'quotation_vendor_id' => $quotationVendor->id
                        ]);
                        $html .= '<a href="' . $createOrderUrl . '" class="btn btn-sm btn-success" 
                            title="' . __('order::request-quotation.send_offer') . '">
                            <i class="uil uil-file-plus m-0"></i>
                        </a>';
                    }
                    
                    // Download file button
                    if ($quotationVendor->requestQuotation->file) {
                        $html .= '<a href="' . asset('storage/' . $quotationVendor->requestQuotation->file) . '" 
                            class="btn btn-sm btn-info" download 
                            title="' . __('order::request-quotation.download_file') . '">
                            <i class="uil uil-download-alt m-0"></i>
                        </a>';
                    }
                    
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['quotation_number', 'customer_info', 'status_badge', 'order_number', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show quotation details
     */
    public function show($lang, $countryCode, $id)
    {
        $vendorId = auth()->user()->vendorByUser?->id ?? auth()->user()->vendorById?->id ?? null;
        
        if (!$vendorId) {
            abort(403, 'Vendor not found');
        }

        $quotationVendor = RequestQuotationVendor::with([
            'requestQuotation.customer',
            'requestQuotation.customerAddress.city',
            'requestQuotation.customerAddress.region',
            'requestQuotation.customerAddress.subregion',
            'requestQuotation.customerAddress.country',
            'order'
        ])
        ->where('vendor_id', $vendorId)
        ->findOrFail($id);

        return view('order::vendor.request-quotations.show', compact('quotationVendor'));
    }

    /**
     * Send offer to customer
     */
    public function sendOffer(Request $request, $lang, $countryCode, $id)
    {
        $request->validate([
            'offer_price' => 'required|numeric|min:0',
            'offer_notes' => 'nullable|string|max:1000',
        ]);

        $vendorId = auth()->user()->vendorByUser?->id ?? auth()->user()->vendorById?->id ?? null;
        
        if (!$vendorId) {
            return response()->json([
                'status' => false,
                'message' => 'Vendor not found',
            ], 403);
        }

        $quotationVendor = RequestQuotationVendor::where('vendor_id', $vendorId)
            ->findOrFail($id);

        if (!$quotationVendor->canSendOffer()) {
            return response()->json([
                'status' => false,
                'message' => __('order::request-quotation.cannot_send_offer'),
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Send offer
            $quotationVendor->sendOffer(
                $request->input('offer_price'),
                $request->input('offer_notes')
            );

            // Send notification to customer using AdminNotification
            $customer = $quotationVendor->requestQuotation->customer;
            if ($customer) {
                \App\Models\AdminNotification::notify(
                    type: 'customer_offer_received',
                    title: 'order::request-quotation.notification_customer_offer_title',
                    description: 'order::request-quotation.notification_customer_offer_message',
                    url: route('admin.request-quotations.view-offers', [
                        'lang' => app()->getLocale(),
                        'countryCode' => $quotationVendor->requestQuotation->country->code ?? 'eg',
                        'id' => $quotationVendor->request_quotation_id,
                    ]),
                    icon: 'uil-envelope-receive',
                    color: 'success',
                    notifiable: $quotationVendor,
                    data: [
                        'vendor' => $quotationVendor->vendor->name,
                        'price' => number_format($quotationVendor->offer_price, 2) . ' ' . currency(),
                        'quotation_vendor_id' => $quotationVendor->id,
                    ],
                    userId: null, // For admin to see
                    vendorId: null
                );
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => __('order::request-quotation.offer_sent_successfully'),
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
     * Create order from quotation (vendor creates order directly)
     */
    public function createOrder(Request $request, $lang, $countryCode, $id)
    {
        $request->validate([
            'order_price' => 'required|numeric|min:0',
            'order_notes' => 'nullable|string|max:1000',
        ]);

        $vendorId = auth()->user()->vendorByUser?->id ?? auth()->user()->vendorById?->id ?? null;
        
        if (!$vendorId) {
            return response()->json([
                'status' => false,
                'message' => 'Vendor not found',
            ], 403);
        }

        $quotationVendor = RequestQuotationVendor::with(['requestQuotation.customer', 'requestQuotation.customerAddress'])
            ->where('vendor_id', $vendorId)
            ->findOrFail($id);

        // Check if order already created
        if ($quotationVendor->order_id) {
            return response()->json([
                'status' => false,
                'message' => __('order::request-quotation.order_already_created'),
            ], 400);
        }

        try {
            DB::beginTransaction();

            $quotation = $quotationVendor->requestQuotation;
            $customer = $quotation->customer;

            // Create order data
            $orderData = [
                'customer_id' => $customer->id,
                'customer_name' => $customer->full_name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                'customer_address' => $quotation->customerAddress?->address,
                'country_id' => $quotation->country_id,
                'city_id' => $quotation->customerAddress?->city_id,
                'region_id' => $quotation->customerAddress?->region_id,
                'subregion_id' => $quotation->customerAddress?->subregion_id,
                'total_price' => $request->input('order_price'),
                'total_product_price' => $request->input('order_price'),
                'payment_type' => 'cash_on_delivery',
                'order_from' => 'vendor_quotation',
                'stage_id' => 1, // Default stage (pending/new)
                'items_count' => 1,
                'shipping' => 0,
                'total_tax' => 0,
                'total_fees' => 0,
                'total_discounts' => 0,
                'notes' => $request->input('order_notes'),
            ];

            // Create order
            $order = \Modules\Order\app\Models\Order::create($orderData);

            // Create vendor order stage
            \Modules\Order\app\Models\VendorOrderStage::create([
                'order_id' => $order->id,
                'vendor_id' => $vendorId,
                'stage_id' => 1, // Default stage
            ]);

            // Mark quotation vendor as order created
            $quotationVendor->markOrderCreated($order->id);

            // Send notification to customer using AdminNotification
            if ($customer) {
                \App\Models\AdminNotification::notify(
                    type: 'quotation_order_created',
                    title: 'order::request-quotation.notification_customer_order_created_title',
                    description: 'order::request-quotation.notification_customer_order_created_message',
                    url: route('admin.orders.show', [
                        'lang' => app()->getLocale(),
                        'countryCode' => $quotation->country->code ?? 'eg',
                        'id' => $order->id,
                    ]),
                    icon: 'uil-shopping-cart',
                    color: 'success',
                    notifiable: $order,
                    data: [
                        'vendor' => $quotationVendor->vendor->name,
                        'order_number' => $order->order_number,
                        'price' => number_format($order->total_price, 2) . ' ' . currency(),
                        'quotation_number' => $quotation->quotation_number,
                    ],
                    userId: null, // For admin
                    vendorId: null
                );
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => __('order::request-quotation.order_created_successfully'),
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'message' => __('common.error_occurred') . ': ' . $e->getMessage(),
            ], 500);
        }
    }
}
