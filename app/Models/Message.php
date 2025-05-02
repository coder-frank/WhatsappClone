<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'is_read',
        'seen_at',
    ];

    public function media()
    {
        return $this->hasOne(Media::class);
    }
}
