<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GeneralNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $notificationTitle;
    public $notificationMessage;
    public $actionUrl;
    public $actionText;

    public function __construct(?User $user, string $title, string $message, ?string $actionUrl = null, ?string $actionText = null)
    {
        $this->user = $user;
        $this->notificationTitle = $title;
        $this->notificationMessage = $message;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->notificationTitle,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.general.notification',
            with: [
                'user' => $this->user,
                'notificationTitle' => $this->notificationTitle,
                'notificationMessage' => $this->notificationMessage,
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
