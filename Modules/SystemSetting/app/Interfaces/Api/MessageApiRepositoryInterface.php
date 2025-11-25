<?php

namespace Modules\SystemSetting\app\Interfaces\Api;

interface MessageApiRepositoryInterface
{
    /**
     * Create a new message
     */
    public function createMessage(array $data);

    /**
     * Get all messages with filtering
     */
    public function getAllMessages(array $filters = []);

    /**
     * Get message by ID
     */
    public function getMessageById(int $id);

    /**
     * Update message
     */
    public function updateMessage(int $id, array $data);

    /**
     * Delete message
     */
    public function deleteMessage(int $id);
}
