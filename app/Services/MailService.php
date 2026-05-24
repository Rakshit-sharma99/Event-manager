<?php

namespace App\Services;

use App\Mail\AdminNotificationMail;
use App\Mail\EventUpdateMail;
use App\Mail\GeneralNotificationMail;
use App\Mail\GuestInvitationMail;
use App\Mail\LoginAlertMail;
use App\Mail\RsvpConfirmationMail;
use App\Mail\UserWelcomeMail;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class MailService
{
    /**
     * Send welcome email to a new user.
     */
    public function sendWelcomeEmail(User $user)
    {
        Mail::to($user->email)->send(new UserWelcomeMail($user));
    }

    /**
     * Send login alert to a user.
     */
    public function sendLoginAlert(User $user, string $ipAddress, string $userAgent)
    {
        Mail::to($user->email)->send(new LoginAlertMail($user, $ipAddress, $userAgent));
    }

    /**
     * Send an RSVP confirmation email to a guest.
     */
    public function sendRsvpConfirmation($guest, Event $event, string $status)
    {
        Mail::to($guest->email)->send(new RsvpConfirmationMail($guest, $event, $status));
    }

    /**
     * Send event update to a guest.
     */
    public function sendEventUpdate($guest, Event $event, string $message)
    {
        Mail::to($guest->email)->send(new EventUpdateMail($guest, $event, $message));
    }

    /**
     * Send an admin notification.
     */
    public function sendAdminNotification(string $title, string $message, array $details = [], ?string $actionUrl = null, ?string $actionText = null)
    {
        $adminEmail = config('mail.from.address'); // Or a specific admin email
        Mail::to($adminEmail)->send(new AdminNotificationMail($title, $message, $details, $actionUrl, $actionText));
    }

    /**
     * Send a general notification.
     */
    public function sendGeneralNotification(?User $user, string $email, string $title, string $message, ?string $actionUrl = null, ?string $actionText = null)
    {
        Mail::to($email)->send(new GeneralNotificationMail($user, $title, $message, $actionUrl, $actionText));
    }

    /**
     * Send guest invitation directly (alternative to notification).
     */
    public function sendGuestInvitation($guest, Event $event)
    {
        $acceptUrl = URL::signedRoute('guests.rsvp', [
            'event' => $event->id,
            'guest' => $guest->id,
            'status' => 'accepted'
        ]);

        $rejectUrl = URL::signedRoute('guests.rsvp', [
            'event' => $event->id,
            'guest' => $guest->id,
            'status' => 'declined'
        ]);

        Mail::to($guest->email)->send(new GuestInvitationMail($guest, $event, $acceptUrl, $rejectUrl));
    }
}
