<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationLog extends Model
{
    protected $fillable = ['automation_rule_id', 'trigger', 'summary', 'context'];

    protected $casts = ['context' => 'array'];

    public function rule()
    {
        return $this->belongsTo(AutomationRule::class, 'automation_rule_id');
    }
}
