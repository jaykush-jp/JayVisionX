<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeArticle extends Model
{
    protected $fillable = [
        'title', 'slug', 'category', 'content', 'source_url',
        'status', 'is_searchable', 'sort_order', 'last_updated_by',
    ];

    protected $casts = [
        'is_searchable' => 'boolean',
    ];

    /** Only published, searchable entries may be used by the public chatbot. */
    public function scopePublished($q)
    {
        return $q->where('status', 'published')->where('is_searchable', true);
    }

    public function scopeCategory($q, $category)
    {
        return $q->where('category', $category);
    }
}
