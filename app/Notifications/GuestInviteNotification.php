<?php

namespace App\Notifications;

use App\Mail\GuestInvitationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class GuestInviteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $acceptUrl = URL::signedRoute('guests.rsvp', [
            'event' => $this->event->id,
            'guest' => $notifiable->id,
            'status' => 'accepted'
        ]);

        $rejectUrl = URL::signedRoute('guests.rsvp', [
            'event' => $this->event->id,
            'guest' => $notifiable->id,
            'status' => 'declined'
        ]);

        return (new GuestInvitationMail($notifiable, $this->event, $acceptUrl, $rejectUrl))
            ->to($notifiable->email);
    }
}
