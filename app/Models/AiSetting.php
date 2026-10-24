<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    protected $fillable = [
        'provider', 'model', 'business_description', 'system_prompt', 'welcome_message',
        'suggested_questions', 'languages', 'response_style', 'max_response_tokens',
        'confidence_threshold', 'fallback_message', 'is_enabled', 'rate_limit_per_minute',
        'daily_message_limit', 'monthly_cost_cap',
    ];

    protected $casts = [
        'suggested_questions' => 'array',
        'languages'           => 'array',
        'is_enabled'          => 'boolean',
        'max_response_tokens' => 'integer',
        'rate_limit_per_minute' => 'integer',
        'daily_message_limit' => 'integer',
        'confidence_threshold' => 'decimal:2',
        'monthly_cost_cap'    => 'decimal:2',
    ];

    /** The single settings row (created on first access if missing). */
    public static function current(): self
    {
        return static::first() ?? static::create([]);
    }
}
