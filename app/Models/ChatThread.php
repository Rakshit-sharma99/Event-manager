<?php

namespace App\Models;

class ChatThread extends BaseModel
{
    protected $collection = 'chat_threads';

    protected $fillable = [
        'booking_id',
        'guest_id',
        'planner_id',
        'vendor_id',
        'event_id',
        'last_message_at',
        'manually_started',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'manually_started' => 'boolean',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function planner()
    {
        return $this->belongsTo(User::class, 'planner_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'thread_id');
    }

    /**
     * Get or create a thread for a booking.
     */
    public static function getOrCreateForBooking(Booking $booking)
    {
        $event = $booking->event;
        $thread = self::where('booking_id', (string) $booking->getKey())->first();
        
        if (!$thread) {
            $thread = self::create([
                'booking_id' => (string) $booking->getKey(),
                'planner_id' => (string) ($event?->user_id ?? $booking->planner_id),
                'vendor_id' => (string) $booking->vendor_id,
                'event_id' => (string) $booking->event_id,
                'manually_started' => false,
            ]);
        }

        // Keep any existing messages updated with thread_id
        ChatMessage::where('booking_id', (string) $booking->getKey())
            ->whereNull('thread_id')
            ->update(['thread_id' => (string) $thread->getKey()]);

        return $thread;
    }

    /**
     * Get or create a thread for a guest.
     */
    public static function getOrCreateForGuest(Guest $guest, bool $manuallyStarted = false)
    {
        $thread = self::where('guest_id', (string) $guest->getKey())->first();
        
        if (!$thread) {
            $event = $guest->event;
            $thread = self::create([
                'guest_id' => (string) $guest->getKey(),
                'planner_id' => (string) ($event?->user_id),
                'event_id' => (string) $guest->event_id,
                'manually_started' => $manuallyStarted,
            ]);
        } elseif ($manuallyStarted && !$thread->manually_started) {
            $thread->update(['manually_started' => true]);
        }

        return $thread;
    }
}
