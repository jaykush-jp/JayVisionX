<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsage extends Model
{
    protected $fillable = [
        'provider', 'model', 'kind', 'input_tokens', 'output_tokens', 'images', 'cost',
    ];

    protected $casts = [
        'input_tokens'  => 'integer',
        'output_tokens' => 'integer',
        'images'        => 'integer',
        'cost'          => 'decimal:6',
    ];
}
