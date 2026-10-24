<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'service_id', 'name', 'tier', 'description', 'price_type', 'amount', 'currency',
        'pricing_unit', 'min_budget', 'max_budget', 'estimated_timeline',
        'features', 'add_ons', 'exclusions', 'tax_note', 'quote_validity',
        'effective_date', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'features'       => 'array',
        'add_ons'        => 'array',
        'exclusions'     => 'array',
        'amount'         => 'decimal:2',
        'min_budget'     => 'decimal:2',
        'max_budget'     => 'decimal:2',
        'effective_date' => 'date',
        'is_active'      => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }

    /** Human-readable price label, e.g. "Starting from ₹25,000" or "Custom Quote". */
    public function getPriceLabelAttribute(): string
    {
        if ($this->price_type === 'custom' || $this->amount === null) {
            return 'Custom Quote';
        }

        $prefix = $this->price_type === 'starting_from' ? 'Starting from ' : '';
        $suffix = match ($this->price_type) {
            'hourly'  => '/hr',
            'monthly' => '/month',
            default   => '',
        };

        return $prefix . $this->currency . ' ' . number_format((float) $this->amount) . $suffix;
    }
}
