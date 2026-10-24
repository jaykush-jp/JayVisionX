<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadNote extends Model
{
    protected $fillable = ['contact_id', 'type', 'body', 'created_by'];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
