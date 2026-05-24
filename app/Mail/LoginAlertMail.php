<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $ipAddress;
    public $userAgent;

    public function __construct(User $user, string $ipAddress, string $userAgent)
    {
        $this->user = $user;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Login Alert - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.login_alert',
            with: [
                'user' => $this->user,
                'ipAddress' => $this->ipAddress,
                'userAgent' => $this->userAgent,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
