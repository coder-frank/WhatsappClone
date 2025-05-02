<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageReply extends Model
{
    protected $fillable = [
        'message_id',
        'replied_to_id',
    ];
    
}
