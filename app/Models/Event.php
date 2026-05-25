<?php

namespace App\Models;

class Event extends BaseModel
{
    protected $collection = 'events';

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'guest_count_expected' => 'integer',
            'total_budget' => 'float',
        ];
    }

    public function planner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getTitleAttribute()
    {
        return $this->event_name;
    }

    public function budgets()
    {
        return $this->hasMany(EventBudget::class, 'event_id');
    }

    public function expenses()
    {
        return $this->hasMany(EventExpense::class, 'event_id');
    }

    public function guests()
    {
        return $this->hasMany(Guest::class, 'event_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'event_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'event_id');
    }

    public function gallery()
    {
        return $this->hasMany(Gallery::class, 'event_id');
    }

    public function serviceSelections()
    {
        return $this->hasMany(EventServiceSelection::class, 'event_id');
    }

    public function budgetAllocations()
    {
        return $this->hasMany(EventBudgetAllocation::class, 'event_id');
    }
}
