<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domain extends Model
{
    protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'expiry_date' => 'datetime',
];
    protected $fillable = [
        'domain',
        'user_id',
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dnsrecords():BelongsTo{
        return $this->belongsTo(dnsRecords::class);
    }
}