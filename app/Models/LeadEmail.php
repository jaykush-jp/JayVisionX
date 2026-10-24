<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadEmail extends Model
{
    protected $fillable = ['contact_id', 'subject', 'body', 'sent_by', 'status'];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
