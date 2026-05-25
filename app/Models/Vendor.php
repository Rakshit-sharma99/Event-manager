<?php

namespace App\Models;

use App\Traits\HasAvatar;

class Vendor extends BaseModel
{
    use HasAvatar;

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

    /**
     * Relationship to custom uploaded portfolio gallery images.
     */
    public function galleryImages()
    {
        return $this->hasMany(VendorGalleryImage::class, 'vendor_id')->orderBy('sort_order');
    }

    /**
     * Merge uploaded and legacy seeded gallery images.
     */
    public function getAllGalleryImagesAttribute(): array
    {
        $uploaded = $this->galleryImages()->pluck('image_path')->map(fn($path) => asset('storage/' . $path))->all();
        $seeded = $this->gallery ?? [];
        
        return array_merge($uploaded, $seeded);
    }
}
