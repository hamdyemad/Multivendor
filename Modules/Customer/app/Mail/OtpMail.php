<?php

namespace Modules\Customer\app\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $email,
        public string $otp,
        public string $type,
        public int $expiresInMinutes = 10,
        public string $language = 'en',
        public ?string $verificationToken = null,
    ) {}

    public function envelope(): Envelope
    {
        // Set locale temporarily for this mailable
        App::setLocale($this->language);

        $subjects = [
            'email_verification' => trans('customer.otp_email.email_verification_subject'),
            'password_reset' => trans('customer.otp_email.password_reset_subject'),
        ];

        $subject = $subjects[$this->type] ?? trans('customer.otp_email.default_subject');

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        // Set locale for view rendering
        App::setLocale($this->language);

        return new Content(
            view: 'customer::emails.otp',
            with: [
                'email' => $this->email,
                'otp' => $this->otp,
                'type' => $this->type,
                'expiresInMinutes' => $this->expiresInMinutes,
                'verificationToken' => $this->verificationToken,
            ],
        );
    }
}

