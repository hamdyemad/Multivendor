<?php

namespace Modules\SystemSetting\app\Repositories\Api;

use Illuminate\Support\Facades\DB;
use Modules\SystemSetting\app\Interfaces\Api\MessageApiRepositoryInterface;
use Modules\SystemSetting\app\Models\Message;

class MessageApiRepository implements MessageApiRepositoryInterface
{
    /**
     * Create a new message
     */
    public function createMessage(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Message::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'title' => $data['title'],
                'content' => $data['content'],
                'status' => 'pending',
            ]);
        });
    }

    /**
     * Get all messages with filtering
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

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->with('user')->latest()->get();
    }

    /**
     * Get message by ID
     */
    public function getMessageById(int $id)
    {
        return Message::with('user')->findOrFail($id);
    }

    /**
     * Update message
     */
    public function updateMessage(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $message = Message::findOrFail($id);
            $message->update($data);
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
}
