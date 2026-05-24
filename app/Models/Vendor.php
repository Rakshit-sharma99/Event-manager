<?php

namespace App\Models;

class Vendor extends BaseModel
{
    protected $collection = 'vendors';

    protected $fillable = [
        'user_id', 'name', 'business_name', 'base_location', 'work_location',
        'budget_min', 'budget_max', 'speciality', 'services_provided',
        'description', 'contact_number', 'portfolio_images', 'contact_email',
        // Legacy fields kept for backward compatibility
        'category', 'location', 'price_min', 'price_max',
        'rating', 'total_reviews', 'availability_json', 'gallery',
    ];

    protected function casts(): array
    {
        return [
            'price_min' => 'float',
            'price_max' => 'float',
            'budget_min' => 'float',
            'budget_max' => 'float',
            'rating' => 'float',
            'total_reviews' => 'integer',
            'availability_json' => 'array',
            'gallery' => 'array',
            'services_provided' => 'array',
            'portfolio_images' => 'array',
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
