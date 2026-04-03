<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        return Message::where('sender_id', $request->user_id)
            ->orWhere('receiver_id', $request->user_id)
            ->orderBy('id', 'desc')
            ->paginate(50);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        return Message::create($data);
    }

    public function destroy(Message $message)
    {
        $message->delete();
        return response()->noContent();
    }
}
