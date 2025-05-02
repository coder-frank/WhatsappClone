<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message;
    public $receiver_id;

    public function __construct(Message $message, $receiver_id)
    {
        $this->message = $message->load('media'); // 👈 load media with it
        $this->receiver_id = $receiver_id;
    }

    public function broadcastOn()
    {
        return ['chat.' . $this->receiver_id];
    }


    public function broadcastAs()
    {
        return 'new-message';
    }

    public function broadcastWith()
    {
        $msg = $this->message;
        $media = $msg->media;

        $preview = null;
        if ($media) {
            $url = asset('storage/' . $media->file_path);
            $preview = match ($media->type) {
                'image' => "<img src='$url' style='max-width:200px;' class='img-fluid rounded' />",
                'video' => "<video controls style='max-width:200px;'><source src='$url' /></video>",
                default => "<a href='$url' class='text-white' target='_blank'>Download Document</a>"
            };
        }

        return [
            'message' => $msg->message ?? $preview,
            'sender_id' => $msg->sender_id,
            'time' => $msg->created_at->format('g:i A'),
        ];
    }
}
