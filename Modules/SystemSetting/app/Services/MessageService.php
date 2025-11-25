<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Interfaces\MessageRepositoryInterface;

class MessageService
{
    protected $messageRepository;

    public function __construct(MessageRepositoryInterface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * Get all messages
     */
    public function getAllMessages(array $filters = [])
    {
        return $this->messageRepository->getAllMessages($filters);
    }

    /**
     * Get message by ID
     */
    public function getMessageById(int $id)
    {
        return $this->messageRepository->getMessageById($id);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(int $id)
    {
        return $this->messageRepository->markAsRead($id);
    }

    /**
     * Archive message
     */
    public function archiveMessage(int $id)
    {
        return $this->messageRepository->archiveMessage($id);
    }

    /**
     * Delete message
     */
    public function deleteMessage(int $id)
    {
        return $this->messageRepository->deleteMessage($id);
    }

    /**
     * Get datatable data
     */
    public function getDatatableData(array $filters = [])
    {
        return $this->messageRepository->getDatatableData($filters);
    }
}
