<?php

namespace App\Models;

class Guest extends BaseModel
{
    protected $collection = 'guests';

    protected function casts(): array
    {
        return ['plus_one_count' => 'integer', 'plus_one_details_json' => 'array'];
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function response()
    {
        return $this->hasOne(GuestResponse::class, 'guest_id');
    }
}
