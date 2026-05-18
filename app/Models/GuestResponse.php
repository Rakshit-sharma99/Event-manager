<?php

namespace App\Models;

class GuestResponse extends BaseModel
{
    protected $collection = 'guest_responses';

    protected function casts(): array
    {
        return ['responded_at' => 'datetime'];
    }
}
