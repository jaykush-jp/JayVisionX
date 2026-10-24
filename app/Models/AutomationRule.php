<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    protected $fillable = [
        'name', 'trigger', 'action', 'message', 'notify_email', 'is_active', 'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public const TRIGGERS = [
        'chat_started'       => 'Chat started',
        'lead_created'       => 'Lead captured',
        'handover_requested' => 'Human handover requested',
        'quote_requested'    => 'Quote requested',
        'low_confidence'     => 'Low-confidence / unanswered',
    ];

    public const ACTIONS = [
        'notify_admin' => 'Notify admin by email',
        'auto_message' => 'Post an auto message in the chat',
        'set_pending'  => 'Mark conversation pending',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }

    public function triggerLabel(): string
    {
        return self::TRIGGERS[$this->trigger] ?? $this->trigger;
    }

    public function actionLabel(): string
    {
        return self::ACTIONS[$this->action] ?? $this->action;
    }
}
