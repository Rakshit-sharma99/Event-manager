<?php

namespace App\Models;

class EventBudget extends BaseModel
{
    protected $collection = 'event_budgets';

    protected function casts(): array
    {
        return ['budgeted_amount' => 'float', 'spent_amount' => 'float'];
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
