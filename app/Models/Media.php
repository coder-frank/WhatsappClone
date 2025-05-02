<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'message_id',
        'type', // image, video, audio, document
        'file_path',
    ];
    
}
