<?php

namespace App\Models;

class Booking extends BaseModel
{
    protected $collection = 'bookings';

    protected function casts(): array
    {
        return ['booking_date' => 'date', 'amount' => 'float'];
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class, 'booking_id');
    }

    /**
     * Get the planner (user) who owns the event this booking belongs to.
     */
    public function planner()
    {
        return $this->event?->planner();
    }

    /**
     * Convenience: get the planner User model.
     */
    public function getPlannerUserAttribute()
    {
        return $this->event?->planner;
    }
}
