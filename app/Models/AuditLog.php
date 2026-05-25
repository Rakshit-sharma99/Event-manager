<?php

namespace App\Models;

class AuditLog extends BaseModel
{
    protected $collection = 'audit_logs';

    protected $fillable = [
        'admin_id',
        'action',        // vendor_approved, vendor_rejected, user_suspended, etc.
        'target_type',   // vendor, user, event, booking
        'target_id',
        'details',       // JSON metadata
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Quick factory for logging admin actions.
     */
    public static function log(string $action, string $targetType, string $targetId, array $details = []): self
    {
        return static::create([
            'admin_id' => (string) auth()->id(),
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }
}
