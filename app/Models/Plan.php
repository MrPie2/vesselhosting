<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table="plans";
    
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}
