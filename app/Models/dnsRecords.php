<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class dnsRecords extends Model
{
    protected $fillable = [
        'domain',
        'user_id',
    ];

    public function dnsrecords(): BelongsTo
    {
        return $this->belongsTo(DnsRecords::class);
    }
}