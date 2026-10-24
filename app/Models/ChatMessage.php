<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_session_id', 'role', 'content', 'sources', 'meta',
    ];

    protected $casts = [
        'sources' => 'array',
        'meta'    => 'array',
    ];

    public function session()
    {
        return $this->belongsTo(ChatSession::class, 'chat_session_id');
    }
}
