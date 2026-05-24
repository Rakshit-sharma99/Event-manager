<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventUpdateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $guest;
    public $event;
    public $updateMessage;

    public function __construct($guest, Event $event, string $updateMessage)
    {
        $this->guest = $guest;
        $this->event = $event;
        $this->updateMessage = $updateMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Event Update: ' . $this->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.events.update',
            with: [
                'guest' => $this->guest,
                'event' => $this->event,
                'updateMessage' => $this->updateMessage,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
