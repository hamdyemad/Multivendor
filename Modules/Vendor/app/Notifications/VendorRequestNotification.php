<?php

namespace Modules\Vendor\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Vendor\app\Models\VendorRequest;

class VendorRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected VendorRequest $vendorRequest)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Vendor Request Received - ' . config('app.name'))
            ->view('emails.vendor-request-simple', [
                'vendorRequest' => $this->vendorRequest,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'vendor_request_id' => $this->vendorRequest->id,
            'email' => $this->vendorRequest->email,
            'company_name' => $this->vendorRequest->company_name,
        ];
    }
}
