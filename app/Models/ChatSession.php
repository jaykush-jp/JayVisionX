<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    protected $fillable = [
        'session_token', 'visitor_name', 'visitor_email', 'visitor_phone',
        'status', 'is_ai_active', 'assigned_to', 'lead_id', 'source_page',
        'ip_address', 'user_agent', 'consent', 'last_message_at',
    ];

    protected $casts = [
        'is_ai_active'    => 'boolean',
        'consent'         => 'boolean',
        'last_message_at' => 'datetime',
    ];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    public function lead()
    {
        return $this->belongsTo(Contact::class, 'lead_id');
    }

    public function scopeStatus($q, $status)
    {
        return $q->where('status', $status);
    }
}
