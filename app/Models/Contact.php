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
        'ai_followups_sent', 'ai_followup_at', 'ai_followup_opt_out',
    ];

    protected $casts = [
        'is_read'             => 'boolean',
        'value'               => 'decimal:2',
        'last_contacted_at'   => 'datetime',
        'follow_up_at'        => 'datetime',
        'ai_followup_at'      => 'datetime',
        'ai_followup_opt_out' => 'boolean',
    ];

    protected static function booted(): void
    {
        // When AI auto follow-up is on, schedule a new lead's first follow-up so
        // the scheduler picks it up on its next run. Only for leads with an email
        // that are still open.
        static::creating(function (Contact $lead) {
            if (empty($lead->ai_followup_at)
                && ! $lead->ai_followup_opt_out
                && ! empty($lead->email)
                && ! in_array($lead->status, ['won', 'lost'], true)
                && Setting::get('ai_followup_enabled')) {
                $lead->ai_followup_at = now();
            }
        });
    }

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
