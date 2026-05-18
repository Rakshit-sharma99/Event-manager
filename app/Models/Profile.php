<?php

namespace App\Models;

class Profile extends BaseModel
{
    protected $collection = 'profiles';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
