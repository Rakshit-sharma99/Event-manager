<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Auth\User as Authenticatable;
use MongoDB\Laravel\Eloquent\SoftDeletes;
use App\Traits\HasAvatar;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasAvatar, SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'phone_number', 'residence',
        'avatar', 'profile_photo', 'profile_complete',
        'email_verified_at', 'jwt_token', 'google_id',
        // OTP fields
        'otp', 'otp_expires_at', 'otp_attempts', 'otp_resend_count', 'otp_last_resent_at',
        // Moderation fields
        'is_suspended', 'is_banned',
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
            'is_suspended' => 'boolean',
            'is_banned' => 'boolean',
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

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
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

    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'user_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'user_id');
    }
}
