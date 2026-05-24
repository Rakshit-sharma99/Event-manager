<?php

namespace App\Models;

class ChatMessage extends BaseModel
{
    protected $collection = 'chat_messages';

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'delivered_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::created(function ($message) {
            if ($message->thread_id) {
                $thread = ChatThread::find($message->thread_id);
                if ($thread) {
                    $thread->update([
                        'last_message_at' => now(),
                    ]);
                }
            }
        });
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function thread()
    {
        return $this->belongsTo(ChatThread::class, 'thread_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
