<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'plans';

    protected $fillable = [
        'name',
        'bandwidth',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
