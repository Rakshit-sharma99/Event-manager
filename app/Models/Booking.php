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
}
