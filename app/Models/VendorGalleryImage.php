<?php

namespace App\Models;

class VendorGalleryImage extends BaseModel
{
    protected $collection = 'vendor_gallery_images';

    protected $fillable = [
        'vendor_id',
        'image_path',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * Relationship back to the Vendor.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
