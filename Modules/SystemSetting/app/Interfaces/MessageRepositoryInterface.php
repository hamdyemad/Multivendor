<?php

namespace Modules\SystemSetting\app\Interfaces;

interface MessageRepositoryInterface
{
    /**
     * Get all messages with pagination
     */
    public function getAllMessages(array $filters = []);

    /**
     * Get message by ID
     */
    public function getMessageById(int $id);

    /**
     * Mark message as read
     */
    public function markAsRead(int $id);

    /**
     * Archive message
     */
    public function archiveMessage(int $id);

    /**
     * Delete message
     */
    public function deleteMessage(int $id);

    /**
     * Get datatable data
     */
    public function getDatatableData(array $filters = []);
}
