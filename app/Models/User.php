<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Auth\User as Authenticatable;
use App\Traits\HasAvatar;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasAvatar;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'phone_number', 'residence',
        'avatar', 'profile_photo', 'profile_complete',
        'email_verified_at', 'jwt_token',
        // OTP fields
        'otp', 'otp_expires_at', 'otp_attempts', 'otp_resend_count', 'otp_last_resent_at',
    ];

    protected $hidden = [
        'password', 'remember_token', 'jwt_token', 'otp',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'otp_last_resent_at' => 'datetime',
            'password' => 'hashed',
            'profile_complete' => 'boolean',
            'otp_attempts' => 'integer',
            'otp_resend_count' => 'integer',
        ];
    }

    /* ── Helpers ─────────────────────────────────── */

    public function isVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function isPlanner(): bool
    {
        return $this->role === 'planner';
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isGuest(): bool
    {
        return $this->role === 'guest';
    }

    /* ── Relationships ───────────────────────────── */

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'user_id');
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'user_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'user_id');
    }
}
