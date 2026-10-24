<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'category', 'icon', 'image', 'banner_image',
        'short_description', 'description', 'features',
        'deliverables', 'tech_stack', 'exclusions', 'related_projects',
        'starting_price', 'price_type', 'pricing_unit', 'currency', 'estimated_delivery',
        'meta_title', 'meta_description', 'meta_keywords',
        'is_active', 'show_on_home', 'sort_order',
    ];

    protected $casts = [
        'features'         => 'array',
        'deliverables'     => 'array',
        'tech_stack'       => 'array',
        'exclusions'       => 'array',
        'related_projects' => 'array',
        'starting_price'   => 'decimal:2',
        'is_active'        => 'boolean',
        'show_on_home'     => 'boolean',
    ];

    public function packages()
    {
        return $this->hasMany(Package::class)->orderBy('sort_order');
    }

    public function faqs()
    {
        return $this->hasMany(Faq::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/service-default.jpg');
    }
}
