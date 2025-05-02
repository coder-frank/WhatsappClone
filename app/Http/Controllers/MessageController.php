<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Media;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function getMessage($friendId)
    {
        $userId = Auth::id();

        $messages = Message::where(function ($q) use ($userId, $friendId) {
            $q->where('sender_id', $userId)->where('receiver_id', $friendId);
        })->orWhere(function ($q) use ($userId, $friendId) {
            $q->where('sender_id', $friendId)->where('receiver_id', $userId);
        })
            ->orderBy('created_at')
            ->get();


        $messages = $messages->map(function ($msg) {
            $type = $msg->media->type ?? null;
            $url = $msg->media ? asset('storage/' . $msg->media->file_path) : null;

            $preview = match ($type) {
                'image' => "<img src='$url' class='img-fluid rounded' style='max-width: 200px;'>",
                'video' => "<video controls style='max-width: 200px;'><source src='$url'></video>",
                'audio' => "<audio controls><source src='$url'></audio>",
                'document' => (str_ends_with($url, '.pdf') ?
                    "<iframe src='$url#toolbar=0' style='width:100%; max-width:250px; height:200px; border:none;'></iframe>" :
                    "<a href='$url' target='_blank'>Preview Document</a>"
                ),
                default => '',
            };

            return [
                'sender_id' => $msg->sender_id,
                'message' => $msg->message ?? $preview,
                'time' => $msg->created_at->format('g:i A'),
            ];
        });

        Message::where('sender_id', $friendId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $messages;
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $msg = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        event(new MessageSent($msg, $request->receiver_id));


        return response()->json([
            'message' => $msg->message,
            'time' => $msg->created_at->format('g:i A')
        ]);
    }

    public function sendMedia(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'file' => 'required|file|max:51200', // 50MB max
        ]);

        $userId = auth()->id();
        $file = $request->file('file');

        $mime = $file->getMimeType();
        $ext = $file->getClientOriginalExtension();

        $type = match (true) {
            str_contains($mime, 'image') => 'image',
            str_contains($mime, 'video') => 'video',
            str_contains($mime, 'audio') || in_array($ext, ['mp3', 'wav', 'webm']) => 'voice',
            str_contains($mime, 'pdf') || str_contains($mime, 'text') => 'document',
            default => 'document',
        };

        $path = $file->store("media", "public");

        $message = Message::create([
            'sender_id' => $userId,
            'receiver_id' => $request->receiver_id,
            'message' => null,
        ]);

        Media::create([
            'message_id' => $message->id,
            'type' => $type,
            'file_path' => $path
        ]);

        event(new MessageSent($message, $request->receiver_id));

        $url = asset('storage/' . $path);

        $preview = match ($type) {
            'image' => "<img src='$url' style='max-width:200px;' class='img-fluid rounded' />",
            'video' => "<video controls style='max-width:200px;'><source src='$url' /></video>",
            'voice' => "
                <div class='custom-audio-player d-flex align-items-center gap-2'>
                    <button class='btn btn-sm btn-outline-light play-audio' data-src='$url'>
                        <i class='fas fa-play'></i>
                    </button>
                    <span class='duration small text-white'>0:00</span>
                    <audio class='d-none' src='$url'></audio>
                </div>
            ",

            default => "<a href='$url' class='text-white' target='_blank'>Download Document</a>"
        };

        return response()->json([
            'preview' => $preview,
            'time' => now()->format('g:i A')
        ]);
    }
}
