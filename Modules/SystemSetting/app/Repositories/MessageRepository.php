<?php

namespace Modules\SystemSetting\app\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\SystemSetting\app\Interfaces\MessageRepositoryInterface;
use Modules\SystemSetting\app\Models\Message;

class MessageRepository implements MessageRepositoryInterface
{
    /**
     * Get all messages with pagination
     */
    public function getAllMessages(array $filters = [])
    {
        $query = Message::query();

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        return $query->latest()->paginate(15);
    }

    /**
     * Get message by ID
     */
    public function getMessageById(int $id)
    {
        return Message::findOrFail($id);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(int $id)
    {
        return DB::transaction(function () use ($id) {
            $message = Message::findOrFail($id);
            $message->update(['status' => 'read']);
            return $message;
        });
    }

    /**
     * Archive message
     */
    public function archiveMessage(int $id)
    {
        return DB::transaction(function () use ($id) {
            $message = Message::findOrFail($id);
            $message->update(['status' => 'archived']);
            return $message;
        });
    }

    /**
     * Delete message
     */
    public function deleteMessage(int $id)
    {
        return DB::transaction(function () use ($id) {
            $message = Message::findOrFail($id);
            $message->delete();
            return $message;
        });
    }

    /**
     * Get datatable data
     */
    public function getDatatableData(array $filters = [])
    {
        $query = Message::query();

        // Search filter
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        // Sorting
        $sortColumn = $filters['sortColumn'] ?? 'created_at';
        $sortDirection = $filters['sortDirection'] ?? 'desc';
        $query->orderBy($sortColumn, $sortDirection);

        // Pagination
        $perPage = $filters['per_page'] ?? 10;
        $page = $filters['page'] ?? 1;

        $total = $query->count();
        $data = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ];
    }
}
