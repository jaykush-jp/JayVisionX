<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['page_name','title','subtitle','banner_image','content','meta_title','meta_description','meta_keywords','og_image'];
    public static function getPage($name) {
        // Never return null: views read $page->meta_title etc. directly.
        return static::where('page_name', $name)->first()
            ?? new static(['page_name' => $name]);
    }
}
