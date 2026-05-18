<?php

namespace App\Models;

class Vendor extends BaseModel
{
    protected $collection = 'vendors';

    protected function casts(): array
    {
        return [
            'price_min' => 'float',
            'price_max' => 'float',
            'rating' => 'float',
            'total_reviews' => 'integer',
            'availability_json' => 'array',
            'gallery' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ratings()
    {
        return $this->hasMany(VendorRating::class, 'vendor_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'vendor_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'vendor_id');
    }
}
