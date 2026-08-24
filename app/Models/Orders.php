<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Orders extends Model
{
    protected $fillable = [
        'domain',
        'user_id',
    ];

    public function orders(): BelongsTo
    {
        return $this->belongsTo(Orders::class);
    }
}