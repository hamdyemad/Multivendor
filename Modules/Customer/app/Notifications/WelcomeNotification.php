<?php

namespace Modules\Customer\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Customer\app\Models\Customer;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function __construct(protected Customer $customer)
    {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name');
        $locale = $notifiable->lang ?? app()->getLocale();

        // Set the locale for this email
        app()->setLocale($locale);

        return (new MailMessage)
            ->subject(__('customer.welcome_email.subject', ['app_name' => $appName]))
            ->view('customer::emails.welcome', [
                'customer' => $notifiable,
                'appName' => $appName,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'customer_id' => $notifiable->id,
            'message' => __('customer.welcome_email.thank_you', ['app_name' => config('app.name')]),
        ];
    }
}
