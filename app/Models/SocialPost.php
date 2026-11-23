<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialPost extends Model
{
    protected $fillable = [
        'body', 'image', 'hashtags', 'location', 'platforms', 'scheduled_at', 'status',
        'external_ids', 'error', 'created_by',
    ];

    /** Full caption sent to channels: body + hashtags. */
    public function getCaptionAttribute(): string
    {
        $caption = trim((string) $this->body);
        if ($this->hashtags) {
            $caption .= "\n\n" . $this->hashtags;
        }
        return $caption;
    }

    protected $casts = [
        'platforms'    => 'array',
        'external_ids' => 'array',
        'scheduled_at' => 'datetime',
    ];

    public const PLATFORMS = [
        'google'    => 'Google Business Profile',
        'facebook'  => 'Facebook',
        'instagram' => 'Instagram',
    ];
}
