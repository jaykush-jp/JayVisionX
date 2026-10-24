<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialPost extends Model
{
    protected $fillable = [
        'body', 'image', 'platforms', 'scheduled_at', 'status',
        'external_ids', 'error', 'created_by',
    ];

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
