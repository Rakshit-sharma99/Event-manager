<?php

namespace App\Models;

class EventBudgetAllocation extends BaseModel
{
    protected $collection = 'event_budget_allocations';

    protected $fillable = [
        'event_id',
        'category',
        'allocated_amount',
        'used_amount',
        'priority_level',  // high, medium, low
        'is_locked',       // boolean — locked categories skip rebalancing
    ];

    protected function casts(): array
    {
        return [
            'allocated_amount' => 'float',
            'used_amount'      => 'float',
            'is_locked'        => 'boolean',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
