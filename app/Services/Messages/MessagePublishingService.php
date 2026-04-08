<?php

namespace App\Services\Messages;

use App\Models\Message;
use App\Models\User;

class MessagePublishingService
{
    public function send(User $sender, User $receiver, string $content): Message
    {
        $message = Message::query()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => $content,
        ]);

        return $message->load(['sender', 'receiver']);
    }
}
