<?php

namespace Modules\SystemSetting\app\Services\Api;

use Modules\SystemSetting\app\Interfaces\Api\MessageApiRepositoryInterface;

class MessageApiService
{
    protected $messageRepository;

    public function __construct(MessageApiRepositoryInterface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * Create a new message
     */
    public function createMessage(array $data)
    {
        $message = $this->messageRepository->createMessage($data);

        return $message;
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
     * Update message
     */
    public function updateMessage(int $id, array $data)
    {
        return $this->messageRepository->updateMessage($id, $data);
    }

    /**
     * Delete message
     */
    public function deleteMessage(int $id)
    {
        return $this->messageRepository->deleteMessage($id);
    }
}
