<?php

namespace App\Models;

class EventExpense extends BaseModel
{
    protected $collection = 'event_expenses';

    protected function casts(): array
    {
        return ['amount' => 'float', 'date' => 'date'];
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
