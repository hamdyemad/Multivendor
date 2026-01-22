<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function __construct(
        protected AdminNotificationService $notificationService
    ) {}

    /**
     * Show notification details
     */
    public function show($lang, $countryCode, $id)
    {
        $notification = AdminNotification::with('notifiable')->findOrFail($id);
        
        // Mark as viewed by current user
        $this->notificationService->markAsViewedBy($id, auth()->id());
        return view('notifications.show', compact('notification'));
    }

    /**
     * Get paginated notifications (for infinite scroll)
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = 10;
        $type = $request->get('type'); // Optional type filter
        
        // Build query
        $query = AdminNotification::notViewedBy(auth()->id())
            ->orderBy('created_at', 'desc');
        
        // Filter by type if provided
        if ($type) {
            $query->where('type', $type);
        }
        
        // Filter by vendor if not admin
        if (isAdmin()) {
            $query->where(function($q) use ($type) {
                if ($type === 'new_order' || $type === 'new_message' || $type === 'vendor_request' || $type === 'withdraw_request') {
                    // For specific admin types, only show those without vendor_id
                    $q->whereNull('vendor_id');
                } else {
                    // For general notifications, show all admin notifications
                    $q->whereNull('vendor_id')
                      ->orWhereIn('type', ['new_refund_request', 'refund_status_changed']);
                }
            });
        } else {
            $vendorId = auth()->user()->vendor->id;
            
            if ($type === 'withdraw_status') {
                // For vendors, show their withdraw status notifications
                $query->where('vendor_id', $vendorId);
            } else {
                $query->where(function($q) use ($vendorId, $type) {
                    $q->where('vendor_id', $vendorId)
                      ->orWhereNull('vendor_id');
                });
                
                // Exclude admin-only types for vendors
                if (!$type) {
                    $query->whereNotIn('type', ['vendor_request', 'new_message']);
                }
            }
        }
        
        // Get paginated results
        $notifications = $query->paginate($perPage);
        
        // Map to array
        $items = $notifications->map(function($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'icon' => $notification->icon,
                'color' => $notification->color,
                'title' => $notification->getTranslatedTitle(),
                'description' => $notification->getTranslatedDescription(),
                'url' => route('admin.notifications.show', [
                    'lang' => app()->getLocale(), 
                    'countryCode' => strtolower(session('country_code', 'eg')), 
                    'id' => $notification->id
                ]),
                'created_at' => $notification->getRawOriginal('created_at'),
            ];
        });
        
        return response()->json([
            'notifications' => $items,
            'current_page' => $notifications->currentPage(),
            'last_page' => $notifications->lastPage(),
            'total' => $notifications->total(),
            'has_more' => $notifications->hasMorePages(),
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function count()
    {
        $query = AdminNotification::notViewedBy(auth()->id());
        
        // Filter by vendor if not admin
        if (isAdmin()) {
            $query->where(function($q) {
                $q->whereNull('vendor_id')
                  ->orWhereIn('type', ['new_refund_request', 'refund_status_changed']);
            });
        } else {
            $vendorId = auth()->user()->vendor->id;
            $query->where(function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId)
                  ->orWhereNull('vendor_id');
            })->whereNotIn('type', ['vendor_request', 'new_message']);
        }
        
        $count = $query->count();
        
        return response()->json(['count' => $count]);
    }

    public function markAsRead(Request $request)
    {
        $notification = AdminNotification::find($request->id);
        
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        AdminNotification::unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
