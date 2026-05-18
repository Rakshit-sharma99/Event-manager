<?php

namespace App\Models;

class VendorRating extends BaseModel
{
    protected $collection = 'vendor_ratings';

    protected function casts(): array
    {
        return ['rating' => 'integer'];
    }
}
