<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $appends = ['name'];

    public function getNameAttribute(): ?string
    {
        return $this->domain;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dnsRecords(): HasMany
    {
        return $this->hasMany(DnsRecord::class);
    }
}
