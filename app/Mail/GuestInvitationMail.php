<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuestInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $guest;
    public $event;
    public $acceptUrl;
    public $rejectUrl;

    public function __construct($guest, Event $event, string $acceptUrl, string $rejectUrl)
    {
        $this->guest = $guest;
        $this->event = $event;
        $this->acceptUrl = $acceptUrl;
        $this->rejectUrl = $rejectUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You are invited to ' . $this->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.guests.invitation',
            with: [
                'guest' => $this->guest,
                'event' => $this->event,
                'acceptUrl' => $this->acceptUrl,
                'rejectUrl' => $this->rejectUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
