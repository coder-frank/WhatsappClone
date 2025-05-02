<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    public function loadDashboard()
    {
        $userId = auth()->id();

        $friends = Friend::where('user_id', $userId)
            ->with('friend:id,name,profile_picture')
            ->get()
            ->map(function ($f) use ($userId) {
                $friend = $f->friend;

                // Get the last message between the user and this friend
                $lastMessage = Message::where(function ($q) use ($userId, $friend) {
                    $q->where('sender_id', $userId)->where('receiver_id', $friend->id);
                })
                    ->orWhere(function ($q) use ($userId, $friend) {
                        $q->where('sender_id', $friend->id)->where('receiver_id', $userId);
                    })
                    ->latest()
                    ->first();

                // Count unread messages sent by the friend to the user
                $unreadCount = Message::where('sender_id', $friend->id)
                    ->where('receiver_id', $userId)
                    ->where('is_read', false)
                    ->count();

                return [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'profile_picture' => $friend->profile_picture,
                    'last_message' => $lastMessage ? $lastMessage->message : null,
                    'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : null,
                    'unread_count' => $unreadCount
                ];
            });

        return view('dashboard', compact('friends'));
    }


    public function startChat(Request $request)
    {
        $request->validate([
            'target' => 'required',
            'message' => 'nullable|string'
        ]);

        $target = $request->target;
        $user = User::where('email', $target)->orWhere('phone', $target)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot chat with yourself.'], 422);
        }

        // Create friendship
        Friend::firstOrCreate([
            'user_id' => auth()->id(),
            'friend_id' => $user->id
        ]);

        // Optional reverse
        Friend::firstOrCreate([
            'user_id' => $user->id,
            'friend_id' => auth()->id()
        ]);

        // Optional: create welcome message
        if ($request->message) {
            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $user->id,
                'message' => $request->message
            ]);
        }

        return response()->json(['success' => true]);
    }
}
