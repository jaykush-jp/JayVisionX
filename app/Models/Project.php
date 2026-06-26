<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// ===== Project Model =====
class Project extends Model {
    use HasFactory;
    protected $fillable = ['title','slug','client_name','category','thumbnail','gallery','short_description','description','technologies','project_url','completion_date','meta_title','meta_description','is_active','show_on_home','sort_order'];
    protected $casts = ['gallery'=>'array','technologies'=>'array','is_active'=>'boolean','show_on_home'=>'boolean'];
    public function scopeActive($q) { return $q->where('is_active',true)->orderBy('sort_order'); }
    public function getThumbnailUrlAttribute() { return $this->thumbnail ? asset('storage/'.$this->thumbnail) : asset('images/project-default.jpg'); }
}
