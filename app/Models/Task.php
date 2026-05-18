<?php

namespace App\Models;

class Task extends BaseModel
{
    protected $collection = 'tasks';

    protected function casts(): array
    {
        return ['due_date' => 'date', 'sort_order' => 'integer'];
    }
}
