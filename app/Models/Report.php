<?php

namespace App\Models;

class Report extends BaseModel
{
    protected $collection = 'reports';

    protected $fillable = [
        'reporter_id',    // user who filed the report
        'target_type',    // vendor, planner, event, booking
        'target_id',
        'reason',
        'description',
        'status',         // open, under_review, resolved, dismissed
        'admin_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}
