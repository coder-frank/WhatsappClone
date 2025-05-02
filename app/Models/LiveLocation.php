<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveLocation extends Model
{
    protected $fillable = [
        'message_id',
        'latitude',
        'longitude',
        'expires_at',
    ];
    
}
