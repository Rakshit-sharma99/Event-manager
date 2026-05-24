<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RsvpConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $guest;
    public $event;
    public $status;

    public function __construct($guest, Event $event, string $status)
    {
        $this->guest = $guest;
        $this->event = $event;
        $this->status = $status;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'RSVP ' . ucfirst($this->status) . ' - ' . $this->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.guests.rsvp_confirmation',
            with: [
                'guest' => $this->guest,
                'event' => $this->event,
                'status' => $this->status,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
