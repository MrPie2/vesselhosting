<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HostingPlan extends Model{
    protected $fillable = [
        'id',
        'domain',
    ];

    public function user():HasMany{
        return $this->belongsTo(User::class);
    }
}