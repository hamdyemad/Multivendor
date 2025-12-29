<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Modules\Customer\app\Services\CustomerService;
use Modules\Vendor\app\Models\Vendor;
use Modules\SystemSetting\app\Http\Requests\PushNotificationRequest;
use Modules\SystemSetting\app\Services\PushNotificationService;
use Yajra\DataTables\Facades\DataTables;

class PushNotificationController extends Controller
{

    public function __construct(
        protected PushNotificationService $pushNotificationService,
        protected CustomerService $customerService,
        protected LanguageService $languageService,
    )
    {
        $this->middleware('can:push-notifications.index')->only(['index', 'datatable']);
        $this->middleware('can:push-notifications.create')->only(['create', 'store']);
        $this->middleware('can:push-notifications.show')->only(['show', 'customersDatatable']);
        $this->middleware('can:push-notifications.delete')->only(['destroy']);
    }

    /**
     * Display all notifications
     */
    public function index()
    {
        return view('systemsetting::push-notifications.index');
    }

    /**
     * Show create notification form
     */
    public function create()
    {
        $customers = $this->customerService->getCustomersQuery(['status' => true])
            ->select('id', 'first_name', 'last_name', 'email')
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->full_name . ' (' . $customer->email . ')',
                ];
            });

        $vendors = Vendor::where('active', '1')
            ->get()
            ->map(function ($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->name . ' (' . $vendor->user?->email . ')',
                ];
            });

        $languages = $this->languageService->getAll();

        return view('systemsetting::push-notifications.create', compact('customers', 'vendors', 'languages'));
    }

    /**
     * Store new notification
     */
    public function store(PushNotificationRequest $request)
    {
        $validated = $request->validatedWithTranslations();

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('push-notifications', 'public');
        }

        try {
            $notification = $this->pushNotificationService->createAndSend($validated);

            return response()->json([
                'status' => true,
                'message' => __('systemsetting::push-notification.sent_successfully'),
                'data' => $notification,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('systemsetting::push-notification.send_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show notification details
     */
    public function show($lang, $countryCode, $id)
    {
        $notification = $this->pushNotificationService->getNotificationById($id);
        $languages = $this->languageService->getAll();
        $customersCount = $notification->customers()->count();
        $vendorsCount = $notification->vendors()->count();
        $viewsCount = $notification->views()->count();
        return view('systemsetting::push-notifications.show', compact('notification', 'languages', 'customersCount', 'vendorsCount', 'viewsCount'));
    }

    /**
     * View notification (for vendors) - marks as viewed
     */
    public function view($lang, $countryCode, $id)
    {
        $notification = $this->pushNotificationService->getNotificationById($id);
        
        // Mark as viewed by current user
        $notification->markAsViewedBy(auth()->id());
        
        $languages = $this->languageService->getAll();
        return view('systemsetting::push-notifications.view', compact('notification', 'languages'));
    }

    /**
     * Delete notification
     */
    public function destroy($lang, $countryCode, $id)
    {
        try {
            $this->pushNotificationService->deleteNotification($id);

            return response()->json([
                'status' => true,
                'message' => __('systemsetting::push-notification.deleted_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('common.error_occurred'),
            ], 500);
        }
    }

    /**
     * DataTable endpoint
     */
    public function datatable(Request $request)
    {
        $query = $this->pushNotificationService->getAllNotifications([
            'search' => $request->input('search_text'),
            'type' => $request->input('type'),
        ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('title_display', function ($notification) {
                $titleEn = $notification->getTranslation('title', 'en') ?? '';
                $titleAr = $notification->getTranslation('title', 'ar') ?? '';
                return '<div>
                    <strong>' . e($titleEn) . '</strong>
                    <br><small class="text-muted">' . e($titleAr) . '</small>
                </div>';
            })
            ->addColumn('type_badge', function ($notification) {
                $badges = [
                    'all' => '<span class="badge badge-info badge-round">' . __('systemsetting::push-notification.type_all') . '</span>',
                    'specific' => '<span class="badge badge-primary badge-round">' . __('systemsetting::push-notification.type_specific') . '</span>',
                    'all_vendors' => '<span class="badge badge-success badge-round">' . __('systemsetting::push-notification.type_all_vendors') . '</span>',
                    'specific_vendors' => '<span class="badge badge-warning badge-round">' . __('systemsetting::push-notification.type_specific_vendors') . '</span>',
                ];
                return $badges[$notification->type] ?? '-';
            })
            ->addColumn('recipients_count', function ($notification) {
                $customerCount = $notification->customers->count();
                $vendorCount = $notification->vendors->count();
                if ($vendorCount > 0) {
                    return $vendorCount . ' ' . __('systemsetting::push-notification.vendors');
                }
                return $customerCount . ' ' . __('systemsetting::push-notification.customers');
            })
            ->addColumn('created_by_name', function ($notification) {
                return $notification->createdBy ? $notification->createdBy->name : '-';
            })
            ->addColumn('created_date', function ($notification) {
                return $notification->created_at;
            })
            ->addColumn('actions', function ($notification) {
                $showUrl = route('admin.system-settings.push-notifications.show', ['push_notification' => $notification->id]);
                $html = '<div class="d-flex gap-2 justify-content-center">';
                
                if (auth()->user()->can('push-notifications.show')) {
                    $html .= '<a href="' . $showUrl . '" class="btn btn-sm btn-primary" title="' . __('common.view') . '">
                        <i class="uil uil-eye m-0"></i>
                    </a>';
                }
                
                if (auth()->user()->can('push-notifications.delete')) {
                    $html .= '<button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $notification->id . '" title="' . __('common.delete') . '">
                        <i class="uil uil-trash m-0"></i>
                    </button>';
                }
                
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['title_display', 'type_badge', 'actions'])
            ->make(true);
    }

    /**
     * Customers DataTable endpoint for notification show page
     */
    public function customersDatatable(Request $request, $lang, $countryCode, $id)
    {
        $notification = $this->pushNotificationService->getNotificationById($id);
        
        $query = $notification->customers()
            ->select('customers.id', 'customers.first_name', 'customers.last_name', 'customers.email', 'customers.phone');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('full_name', function ($customer) {
                return $customer->full_name;
            })
            ->make(true);
    }

    /**
     * Vendors DataTable endpoint for notification show page
     */
    public function vendorsDatatable(Request $request, $lang, $countryCode, $id)
    {
        $notification = $this->pushNotificationService->getNotificationById($id);
        
        $query = $notification->vendors()
            ->with('user');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('name', function ($vendor) {
                return $vendor->name;
            })
            ->addColumn('email', function ($vendor) {
                return $vendor->user?->email ?? '-';
            })
            ->make(true);
    }

    /**
     * Views DataTable endpoint for notification show page
     */
    public function viewsDatatable(Request $request, $lang, $countryCode, $id)
    {
        $notification = $this->pushNotificationService->getNotificationById($id);
        
        // Get the views with pivot data
        $views = $notification->views()->with(['vendorByUser', 'vendorById'])->get();

        return DataTables::of($views)
            ->addIndexColumn()
            ->addColumn('name', function ($user) {
                // Get vendor (either by user_id or vendor_id)
                $vendor = $user->vendorByUser ?? $user->vendorById;
                
                // If user has vendor, get name from vendor translations
                if ($vendor) {
                    return $vendor->getTranslation('name', app()->getLocale()) ?? $vendor->getTranslation('name', 'en');
                }
                // Otherwise get name from user translations
                return $user->getTranslation('name', app()->getLocale()) ?? $user->getTranslation('name', 'en') ?? $user->email;
            })
            ->addColumn('email', function ($user) {
                return $user->email;
            })
            ->addColumn('viewed_at', function ($user) {
                $viewedAt = $user->pivot->created_at ?? null;
                return $viewedAt ? \Carbon\Carbon::parse($viewedAt)->format('d M, Y, h:i A') : '-';
            })
            ->make(true);
    }
}
