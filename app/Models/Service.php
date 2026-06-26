<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'icon', 'image', 'banner_image',
        'short_description', 'description', 'features',
        'meta_title', 'meta_description', 'meta_keywords',
        'is_active', 'show_on_home', 'sort_order',
    ];

    protected $casts = [
        'features'     => 'array',
        'is_active'    => 'boolean',
        'show_on_home' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/service-default.jpg');
    }
}
