<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hosting extends Model
{
    protected $table = 'hostings';

    protected $fillable = [
        'domain',
        'domain_id',
        'user_id',
        'server_hostname',
        'username',
        'expiry_date',
        'plan_id',
        'password',
        'duration',
        'status',
        'provisioning_status',
        'provisioning_error',
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    /**
     * The domain attached to this hosting account.
     *
     * Hostings store the related domain ID in domain_id.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }

    /**
     * Backwards-compatible explicit relationship name.
     */
    public function domainRelation(): BelongsTo
    {
        return $this->domain();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
