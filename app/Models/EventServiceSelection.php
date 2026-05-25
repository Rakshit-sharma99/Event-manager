<?php

namespace App\Models;

class EventServiceSelection extends BaseModel
{
    protected $collection = 'event_service_selections';

    protected $fillable = [
        'event_id',
        'service_name',
        'priority_level', // high, medium, low
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
