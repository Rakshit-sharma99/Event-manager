<?php

namespace App\Models;

class Notification extends BaseModel
{
    protected $collection = 'notifications';

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }
}
