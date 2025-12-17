<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use Illuminate\Http\Request;
use Modules\SystemSetting\app\Services\MessageService;

class MessageController
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * Display messages list
     */
    public function index()
    {
        return view('systemsetting::messages.index');
    }

    /**
     * Get datatable data
     */
    /**
     * Get datatable data
     */
    public function datatable(Request $request)
    {
        $query = \Modules\SystemSetting\app\Models\Message::query();
        
        // Search filter
        if ($request->filled('search')) {
             $search = $request->get('search');
             $query->where(function($q) use ($search) {
                 $q->where('name', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%")
                   ->orWhere('title', 'like', "%{$search}%");
             });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Created date from filter
        if ($request->filled('created_date_from')) {
            $query->whereDate('created_at', '>=', $request->get('created_date_from'));
        }

        // Created date to filter
        if ($request->filled('created_date_to')) {
            $query->whereDate('created_at', '<=', $request->get('created_date_to'));
        }

        return \Yajra\DataTables\Facades\DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('created_at', function ($message) {
                return $message->created_at;
            })
            ->addColumn('action', function ($message) {
                $actions = '<div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">';
                
                $actions .= '<a href="' . route('admin.messages.show', $message->id) . '" 
                    class="btn btn-primary table_action_father" 
                    title="' . __('systemsetting::messages.view') . '">';
                $actions .= '<i class="uil uil-eye table_action_icon"></i>';
                $actions .= '</a>';

                if ($message->status === 'pending') {
                    $actions .= '<a href="javascript:void(0);" 
                        data-url="' . route('admin.messages.mark-read', $message->id) . '" 
                        class="mark-read-message btn btn-success table_action_father" 
                        title="' . __('systemsetting::messages.mark_as_read') . '">';
                    $actions .= '<i class="uil uil-check table_action_icon"></i>';
                    $actions .= '</a>';
                }

                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show message details
     */
    public function show($lang, $countryCode, $id)
    {
        try {
            $message = $this->messageService->getMessageById($id);

            // Mark as read
            if ($message->status === 'pending') {
                $this->messageService->markAsRead($id);
            }

            return view('systemsetting::messages.show', compact('message'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mark message as read
     */
    public function markAsRead($lang, $countryCode, $id)
    {
        try {
            $this->messageService->markAsRead($id);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => __('systemsetting::messages.mark_read_success')
                ]);
            }

            return redirect()->route('messages.index')
                ->with('success', __('systemsetting::messages.mark_read_success'));
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => __('systemsetting::messages.mark_read_error')
                ], 422);
            }
            return redirect()->back()
                ->with('error', __('systemsetting::messages.mark_read_error'));
        }
    }

    /**
     * Archive message
     */
    public function archive($id)
    {
        try {
            $this->messageService->archiveMessage($id);
            return redirect()->route('admin.messages.index')
                ->with('success', __('systemsetting::messages.archive_success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('systemsetting::messages.archive_error'));
        }
    }

    /**
     * Delete message
     */
    public function destroy($id)
    {
        try {
            $this->messageService->deleteMessage($id);

            // Return JSON for AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('systemsetting::messages.delete_success'),
                    'redirect' => route('admin.messages.index')
                ]);
            }

            return redirect()->route('admin.messages.index')
                ->with('success', __('systemsetting::messages.delete_success'));
        } catch (\Exception $e) {
            // Return JSON for AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('systemsetting::messages.delete_error')
                ], 422);
            }

            return redirect()->back()
                ->with('error', __('systemsetting::messages.delete_error'));
        }
    }
}
