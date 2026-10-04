<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DomainPrice extends Model
{
    protected $fillable = [
        'tld',
        'registration_price',
        'renewal_price',
        'transfer_price',
        'active',
    ];

    public function scopeForTld(Builder $query, string $tld): Builder
    {
        $normalized = '.' . ltrim(strtolower(trim($tld)), '.');

        return $query->whereRaw("LOWER(TRIM(tld)) = ?", [$normalized]);
    }

    protected $casts = [
        'registration_price' => 'decimal:2',
        'renewal_price' => 'decimal:2',
        'transfer_price' => 'decimal:2',
        'active' => 'boolean',
    ];
}
