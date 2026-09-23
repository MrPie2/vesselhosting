<?php

namespace App\Models;
use App\Models\Plan;
use App\Models\Domain;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hosting extends Model
{
    protected $table="hostings";

    protected $fillable = [
        'domain',
        'user_id',
        'server_hostname',
        'username',
        'expiry_date',
        'plan_id',
        'password',
        'duration',
        'status'

    ];

    public function hosting(): BelongsTo
    {
        return $this->belongsTo(Hosting::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }
}