<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\SoftDeletes;

class Event extends BaseModel
{
    use SoftDeletes;

    protected $collection = 'events';

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'event_end_date' => 'date',
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

    /**
     * Get event cover photo URL.
     */
    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }

        if ($this->banner) {
            if (str_starts_with($this->banner, 'http://') || str_starts_with($this->banner, 'https://')) {
                return $this->banner;
            }
            return asset('storage/' . $this->banner);
        }

        // Stunning default high-res cover placeholder
        return 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1200&q=80';
    }

    /**
     * Get event date range formatted cleanly.
     */
    public function getEventDateRangeAttribute(): string
    {
        $start = $this->event_date;
        $end = $this->event_end_date;

        if (!$start) {
            return '';
        }

        if (!$end || $start->equalTo($end)) {
            return $start->format('M d, Y');
        }

        // Format dates beautifully
        if ($start->format('Y') === $end->format('Y')) {
            if ($start->format('M') === $end->format('M')) {
                return $start->format('M d') . ' - ' . $end->format('d, Y');
            }
            return $start->format('M d') . ' - ' . $end->format('M d, Y');
        }

        return $start->format('M d, Y') . ' - ' . $end->format('M d, Y');
    }
}
