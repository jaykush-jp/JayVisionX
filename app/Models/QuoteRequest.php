<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    protected $fillable = [
        'chat_session_id', 'contact_id', 'name', 'email', 'phone',
        'project_type', 'answers', 'estimated_min', 'estimated_max',
        'currency', 'status', 'admin_notes',
    ];

    protected $casts = [
        'answers'       => 'array',
        'estimated_min' => 'decimal:2',
        'estimated_max' => 'decimal:2',
    ];

    public const PROJECT_TYPES = [
        'website' => 'Website Development',
        'android' => 'Android App',
        'webapp'  => 'Web Application',
        'other'   => 'Other / Custom',
    ];

    public const STATUSES = [
        'new'      => 'New',
        'reviewed' => 'Reviewed',
        'sent'     => 'Quote Sent',
        'won'      => 'Won',
        'lost'     => 'Lost',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function session()
    {
        return $this->belongsTo(ChatSession::class, 'chat_session_id');
    }

    public function estimateLabel(): string
    {
        if ($this->estimated_min === null && $this->estimated_max === null) {
            return 'Custom quote';
        }
        $cur = $this->currency ?: 'INR';
        if ($this->estimated_min && $this->estimated_max && $this->estimated_min != $this->estimated_max) {
            return $cur . ' ' . number_format((float) $this->estimated_min) . ' – ' . number_format((float) $this->estimated_max);
        }
        $one = $this->estimated_max ?: $this->estimated_min;
        return $cur . ' ' . number_format((float) $one);
    }

    public function projectLabel(): string
    {
        return self::PROJECT_TYPES[$this->project_type] ?? ucfirst($this->project_type);
    }
}
