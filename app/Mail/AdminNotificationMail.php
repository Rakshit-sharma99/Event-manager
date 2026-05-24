<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $notificationTitle;
    public $notificationMessage;
    public $details;
    public $actionUrl;
    public $actionText;

    public function __construct(string $title, string $message, array $details = [], ?string $actionUrl = null, ?string $actionText = null)
    {
        $this->notificationTitle = $title;
        $this->notificationMessage = $message;
        $this->details = $details;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Admin Alert: ' . $this->notificationTitle,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.notification',
            with: [
                'notificationTitle' => $this->notificationTitle,
                'notificationMessage' => $this->notificationMessage,
                'details' => $this->details,
                'actionUrl' => $this->actionUrl,
                'actionText' => $this->actionText,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
