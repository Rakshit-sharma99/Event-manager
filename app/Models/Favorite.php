<?php

namespace App\Models;

class Favorite extends BaseModel
{
    protected $collection = 'favorites';

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
