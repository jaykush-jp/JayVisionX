<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseStudy extends Model
{
    protected $fillable = ['title','slug','client','industry','thumbnail','challenge','solution','result','content','stats','meta_title','meta_description','is_active','sort_order'];
    protected $casts = ['stats'=>'array','is_active'=>'boolean'];
    public function scopeActive($q) { return $q->where('is_active',true)->orderBy('sort_order'); }
    public function getThumbnailUrlAttribute() { return $this->thumbnail ? asset('storage/'.$this->thumbnail) : asset('images/case-default.jpg'); }
}
