<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hosting extends Model
{
    protected $fillable = [
        'domain',
        'user_id',
    ];

    public function hosting(): BelongsTo
    {
        return $this->belongsTo(Hosting::class);
    }
}