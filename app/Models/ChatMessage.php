<?php

namespace App\Models;

class ChatMessage extends BaseModel
{
    protected $collection = 'chat_messages';

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
