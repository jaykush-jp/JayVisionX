<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ['name','phone','email','service_interested','budget','message','is_read','ip_address'];
    protected $casts = ['is_read' => 'boolean'];
}
