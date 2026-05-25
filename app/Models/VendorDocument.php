<?php

namespace App\Models;

class VendorDocument extends BaseModel
{
    protected $collection = 'vendor_documents';

    protected $fillable = [
        'vendor_id',
        'document_type',   // govt_id, pan, aadhaar, gst, business_license
        'file_path',       // private storage path
        'original_filename',
        'uploaded_at',
        'status',          // pending, reviewed
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
