<?php

namespace Modules\SystemSetting\app\Observers;

use Modules\SystemSetting\app\Models\Message;
use App\Services\AdminNotificationService;

class MessageObserver
{
    public function __construct(
        protected AdminNotificationService $notificationService
    ) {}

    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        // Create admin notification for new message
        if ($message->status === 'pending') {
            $this->createMessageNotification($message);
        }
    }

    /**
     * Handle the Message "updated" event.
     */
    public function updated(Message $message): void
    {
        // If message status changed from pending, mark notification as read
        if ($message->wasChanged('status') && $message->status !== 'pending') {
            $this->notificationService->markTypeAsRead('new_message', $message);
        }
    }

    /**
     * Create admin notification for new message
     */
    protected function createMessageNotification(Message $message): void
    {
        $this->notificationService->create(
            type: 'new_message',
            title: 'menu.new_message', // Translation key
            description: 'menu.sent_new_message', // Translation key
            url: $this->notificationService->generateAdminUrl('admin.messages.show', ['message' => $message]),
            icon: 'uil-envelope',
            color: 'success',
            notifiable: $message,
            data: [
                'common.message_id' => $message->id, // Translation key prefix
                'common.name' => $message->name,
                'common.email' => $message->email,
            ],
            vendorId: null
        );
    }
}
