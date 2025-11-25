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
    public function datatable(Request $request)
    {
        try {
            $filters = [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
                'sortColumn' => $request->get('sortColumn', 'created_at'),
                'sortDirection' => $request->get('sortDirection', 'desc'),
                'per_page' => $request->get('per_page', 10),
                'page' => $request->get('page', 1),
            ];

            $result = $this->messageService->getDatatableData($filters);

            // Format data for datatable
            $data = $result['data']->map(function ($message, $index) use ($result) {
                return [
                    'row_number' => (($result['current_page'] - 1) * $result['per_page']) + $index + 1,
                    'id' => $message->id,
                    'title' => $message->title,
                    'content' => $message->content,
                    'name' => $message->name ?? 'N/A',
                    'email' => $message->email ?? 'N/A',
                    'status' => $message->status,
                    'created_at' => $message->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'total' => $result['total'],
                'recordsFiltered' => $result['total'],
                'current_page' => $result['current_page'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show message details
     */
    public function show($id)
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
    public function markAsRead($id)
    {
        try {
            $this->messageService->markAsRead($id);
            return redirect()->route('admin.messages.index')
                ->with('success', __('systemsetting::messages.mark_read_success'));
        } catch (\Exception $e) {
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
