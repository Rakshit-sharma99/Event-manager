<?php

namespace App\Models;

use App\Traits\HasAvatar;

class Guest extends BaseModel
{
    use HasAvatar;

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
