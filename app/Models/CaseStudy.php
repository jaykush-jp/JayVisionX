<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseStudy extends Model
{
    protected $fillable = ['title','slug','client','industry','category','thumbnail','challenge','solution','result','content','stats','meta_title','meta_description','is_active','sort_order'];

    /** Common work categories used for the Our Work filter. */
    public const CATEGORIES = [
        'Website', 'Web App', 'Mobile App', 'Google Ads', 'Meta Ads',
        'SEO', 'Google Business Profile', 'Social Media', 'Graphic Design', 'Branding', 'Video Editing',
    ];
    protected $casts = ['stats'=>'array','is_active'=>'boolean'];
    public function scopeActive($q) { return $q->where('is_active',true)->orderBy('sort_order')->orderByDesc('id'); }
    public function getThumbnailUrlAttribute() { return $this->thumbnail ? asset('storage/'.$this->thumbnail) : asset('images/case-default.jpg'); }
}
