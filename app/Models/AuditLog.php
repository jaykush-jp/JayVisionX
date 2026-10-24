<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'action', 'entity_type', 'entity_id', 'performed_by', 'changes', 'ip_address',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    /** Convenience helper to record an administrative change. */
    public static function record(string $action, string $entityType, $entityId = null, array $changes = []): self
    {
        return static::create([
            'action'       => $action,
            'entity_type'  => $entityType,
            'entity_id'    => $entityId,
            'performed_by' => session('admin_email'),
            'changes'      => $changes ?: null,
            'ip_address'   => request()->ip(),
        ]);
    }
}
