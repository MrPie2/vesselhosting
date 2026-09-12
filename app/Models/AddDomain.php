<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddDomain extends Model
{
    protected $table="domains";

    protected $fillable = [
        'domain',
        'user_id',
        'expiry_date',
        'status',
    ];
}
