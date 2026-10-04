<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DnsRecord extends Model
{
    protected $fillable = [
        'domain_id',
        'type',
        'name',
        'content',
        'priority',
        'provider_status',
        'provider_error',
    ];

    protected $casts = [
        'priority' => 'integer',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
