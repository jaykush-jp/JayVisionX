<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'service_interested', 'budget', 'message',
        'is_read', 'ip_address',
        'status', 'source', 'value', 'last_contacted_at', 'follow_up_at',
    ];

    protected $casts = [
        'is_read'           => 'boolean',
        'value'             => 'decimal:2',
        'last_contacted_at' => 'datetime',
        'follow_up_at'      => 'datetime',
    ];

    /** Pipeline stages, in order, with a label + badge colour key. */
    public const STATUSES = [
        'new'       => 'New',
        'contacted' => 'Contacted',
        'qualified' => 'Qualified',
        'proposal'  => 'Proposal Sent',
        'won'       => 'Won',
        'lost'      => 'Lost',
    ];

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class)->latest();
    }

    public function emails(): HasMany
    {
        return $this->hasMany(LeadEmail::class)->latest();
    }

    public function scopeStatus($query, ?string $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeDueFollowUps($query)
    {
        return $query->whereNotNull('follow_up_at')
            ->whereNotIn('status', ['won', 'lost'])
            ->where('follow_up_at', '<=', now());
    }

    public function isCustomer(): bool
    {
        return $this->status === 'won';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }
}
