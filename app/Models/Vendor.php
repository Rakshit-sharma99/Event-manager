<?php

namespace App\Models;

use App\Traits\HasAvatar;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Vendor extends BaseModel
{
    use HasAvatar, SoftDeletes;

    protected $collection = 'vendors';

    protected $fillable = [
        'user_id', 'name', 'business_name', 'base_location', 'work_location',
        'budget_min', 'budget_max', 'speciality', 'services_provided',
        'description', 'contact_number', 'portfolio_images', 'contact_email',
        // Legacy fields kept for backward compatibility
        'category', 'location', 'price_min', 'price_max',
        'rating', 'total_reviews', 'availability_json', 'gallery',
        // Verification & Activation fields
        'verification_status', 'is_verified', 'is_active',
        'verified_at', 'verified_by', 'rejection_reason',
        'verification_documents', 'admin_notes',
        'verification_expires_at',
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
            'verification_documents' => 'array',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'verified_at' => 'datetime',
            'verification_expires_at' => 'datetime',
        ];
    }

    /* ── Verification Helpers ──────────────────────── */

    /**
     * Check if vendor is fully active (verified AND active).
     */
    public function isFullyActive(): bool
    {
        return $this->is_verified === true && $this->is_active === true;
    }

    /**
     * Scope to only verified & active vendors (for public directory).
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true)->where('is_active', true);
    }

    /**
     * Calculate vendor profile completion percentage.
     */
    public function getCompletionPercentageAttribute(): int
    {
        $fields = [
            'business_name' => 10,
            'category' => 10,
            'base_location' => 5,
            'work_location' => 5,
            'speciality' => 10,
            'description' => 10,
            'contact_number' => 10,
            'contact_email' => 10,
            'budget_min' => 5,
            'budget_max' => 5,
        ];

        $score = 0;
        foreach ($fields as $field => $weight) {
            if (!empty($this->$field)) {
                $score += $weight;
            }
        }

        // Services provided (array check)
        if (!empty($this->services_provided) && count($this->services_provided) > 0) {
            $score += 10;
        }

        // Portfolio/gallery images
        $galleryCount = $this->galleryImages()->count();
        if ($galleryCount > 0) {
            $score += min(10, $galleryCount * 2); // 2% per image, max 10%
        }

        return min(100, $score);
    }

    /* ── Relationships ─────────────────────────────── */

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

    public function documents()
    {
        return $this->hasMany(VendorDocument::class, 'vendor_id');
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
